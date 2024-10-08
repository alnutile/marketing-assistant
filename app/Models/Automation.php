<?php

namespace App\Models;

use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Automation extends Model
{
    use HasFactory;
    use HasSlug;

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'feedback_required' => 'boolean',
        'scheduled' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedback(): MorphMany
    {
        return $this->morphMany(Feedback::class, 'feedbackable');
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function () {
                return Str::random(12);
            })
            ->doNotGenerateSlugsOnUpdate()
            ->saveSlugsTo('slug');
    }

    public function passedFeedbackCount(): int
    {
        return $this->feedback()
            ->latest()
            ->where('rating', true)
            ->limit(config('assistant.feedback_count'))
            ->count();
    }

    public function run(string $payload): void
    {
        //create automation_runs but in a moment
        if ($this->enabled) {

            $prompt = $this->prompt;
            $context = $payload;

            $dateTime = now()->toDateTimeString();

            $prompt = <<<PROMPT
Date and time: $dateTime

Below is the prompt from an automation and the context if needed for the prompt

<PROMPT FROM THE AUTOMATION>
$prompt

<CONTEXT FROM THE PAYLOAD IF ANY>
$context
PROMPT;
            try {
                $automationRun = AutomationRun::create([
                    'automation_id' => $this->id,
                    'payload' => $prompt,
                    'status' => 'pending',
                ]);
            } catch (\Exception $e) {
                Log::error('Automation run logging failed', [
                    'automation' => $this->name,
                    'error' => $e->getMessage(),
                ]);
            }

            if ($this->feedback_required && $this->passedFeedbackCount() < config('assistant.feedback_count')) {
                Log::info('Automation enabled but count not met', [
                    'automation' => $this->name,
                    'count' => $this->passedFeedbackCount(),
                ]);

                $prompt .= <<<PROMPT
<system prompt>
Below is a prompt that we are just testing your response to. Not tools will be used but you can list off the
tools you would use if you were to use them. We are just testing right now how well the response is going.

## Actual Prompt
$prompt

PROMPT;
                $this->project->addInput(
                    message: $prompt,
                    role: RoleEnum::User,
                );

                $response = LlmDriverFacade::driver(config('llmdriver.driver'))
                    ->completion($prompt);

                $this->project->addInput(
                    message: $response->content,
                    role: RoleEnum::Assistant,
                );

            } else {
                if ($this->feedback_required) {
                    $feedback = $this->feedback()
                        ->limit(config('assistant.feedback_count'))
                        ->get()
                        ->map(function ($feedback) {
                            return $feedback->comment;
                        })->join("\n###### END EXAMPLE ######\n");

                    $prompt = <<<PROMPT
$prompt

## Examples of results that got positive feedback
$feedback
PROMPT;
                }

                Orchestrate::handle(
                    project: $this->project,
                    prompt: $prompt
                );
            }

            try {
                /** @phpstan-ignore-next-line */
                if ($automationRun?->id) {
                    $automationRun->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Automation run logging failed', [
                    'automation' => $this->name,
                    'error' => $e->getMessage(),
                ]);
            }

        } else {
            Log::info('Automation not enabled', [
                'automation' => $this->name,
            ]);

            AutomationRun::create([
                'automation_id' => $this->id,
                'payload' => $payload,
                'status' => 'failed',
                'completed_at' => now(),
            ]);
        }
    }
}
