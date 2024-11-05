<?php

namespace App\Models;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Services\LlmServices\Requests\MessageInDto;
use App\Services\LlmServices\RoleEnum;
use EchoLabs\Prism\ValueObjects\Messages\AssistantMessage;
use EchoLabs\Prism\ValueObjects\Messages\UserMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'status' => StatusEnum::class,
        'chat_status' => ChatStatusEnum::class,
        'product_or_service' => ProductServiceEnum::class,
    ];

    public function schedule_logs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'loggable');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::Active);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function getContext(): string
    {
        $campaignContext = <<<CAMPAIGN_CONTEXT
        ## Unique Selling Proposition (USP)
        {$this->name}
        ## Timeline
        {$this->start_date?->format('Y-m-d')}
        {$this->end_date?->format('Y-m-d')}
        ## DETAILS
        {$this->content}
        ## Product of Service
        {$this->product_or_service?->value}
         ## Target Audience
        {$this->target_audience}
        ## Budget
        {$this->budget}
CAMPAIGN_CONTEXT;

        return $campaignContext;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    protected function createSystemMessageIfNeeded(string $systemPrompt): void
    {
        if ($this->messages()->count() == 0) {

            $this->messages()->create(
                [
                    'content' => $systemPrompt,
                    'role' => \App\Services\LlmServices\RoleEnum::System,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    public function addInput(
        string $message,
        RoleEnum $role = RoleEnum::User,
        ?string $tool_id = '',
        ?string $tool_name = '',
        ?array $tool_args = [],
        ?string $systemPrompt = null,
        ?User $user = null,
        bool $created_by_tool = false): Message
    {

        return DB::transaction(function () use (
            $message,
            $role,
            $tool_id,
            $tool_name,
            $tool_args,
            $systemPrompt,
            $user,
            $created_by_tool) {

            if ($systemPrompt) {
                $this->createSystemMessageIfNeeded($systemPrompt);
            }

            return $this->messages()->create(
                [
                    'content' => $message,
                    'role' => $role,
                    'user_id' => $user?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tool_id' => $tool_id,
                    'tool_name' => $tool_name,
                    'tool_args' => $tool_args,
                    'created_by_tool' => $created_by_tool,
                ]);
        });

    }

    public function getPrismMessage(int $limit = 10): array
    {
        return $this->messages()
            ->limit($limit)
            ->whereIn('role', [
                \App\Services\LlmServices\RoleEnum::User->value,
                \App\Services\LlmServices\RoleEnum::Assistant->value,
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->transform(function ($message) {
                if ($message->role == \App\Services\LlmServices\RoleEnum::User) {
                    return new UserMessage($message->content);
                } else {
                    return new AssistantMessage($message->content);
                }
            })->toArray();
    }

    public function getMessageThread(int $limit = 10): array
    {
        $latestMessages = $this->messages()
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $latestMessagesArray = [];

        foreach ($latestMessages as $message) {
            /**
             * @NOTE
             * I am super verbose here due to an odd BUG
             * I keep losing the data due to some
             * magic toArray() method that
             * was not working
             */
            $asArray = [
                'role' => $message->role->value,
                'content' => $message->content,
                'tool_id' => $message->tool_id,
                'tool' => $message->tool_name,
                'args' => $message->tool_args ?? [],
            ];

            $dto = new MessageInDto(
                content: $asArray['content'],
                role: $asArray['role'],
                tool_id: $asArray['tool_id'],
                tool: $asArray['tool'],
                args: $asArray['args'],
            );

            $latestMessagesArray[] = $dto;
        }

        return array_reverse($latestMessagesArray);

    }
}
