<?php

namespace App\Services\LlmServices\Orchestration;

use App\Events\ScheduleLogEvent;
use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate as OrchestrateFacade;
use Illuminate\Support\Facades\Log;

class Orchestrate
{
    protected bool $logScheduler = false;

    public function setLogScheduler(bool $logScheduler): self
    {
        $this->logScheduler = $logScheduler;

        return $this;
    }

    public function handle(Project $project,
        string $prompt = '',
        RoleEnum $role = RoleEnum::User): void
    {
        if (! empty($prompt)) {
            $project->addInput(
                message: $prompt,
                role: $role,
                user: (auth()->check()) ? auth()->user() : $project->user
            );
        }

        $messages = $project->getMessageThread();

        $systemPrompt = $project->system_prompt;

        $currentDateTime = sprintf('Current date and time: %s', now()->toDateTimeString());

        $systemPrompt = <<<SYSTEM_PROMPT
Current date and time: {$currentDateTime}

System prompt:
$systemPrompt
SYSTEM_PROMPT;

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->setSystem($systemPrompt)
            ->chat($messages);

        $project->addInput(
            message: $response->content,
            role: RoleEnum::Assistant,
        );

        if ($this->logScheduler) {
            ScheduleLogEvent::dispatch($project, $project->toArray());
        }

        if (! empty($response->tool_calls)) {

            Log::info('Orchestration Tools Found', [
                'tool_calls' => collect($response->tool_calls)
                    ->pluck('name')->toArray(),
            ]);

            $count = 1;
            foreach ($response->tool_calls as $tool_call) {
                Log::info('[LaraChain] - Tool Call '.$count, [
                    'tool_call' => $tool_call->name,
                    'tool_count' => count($response->tool_calls),
                ]);

                $tool = app()->make($tool_call->name);

                $functionResponse = $tool->handle($project, $tool_call->arguments);

                $project->addInput(
                    message: sprintf('Tool %s used with results %s', $tool_call->name, $functionResponse->content),
                    role: RoleEnum::User,
                    tool_id: $tool_call->id,
                    tool_name: $tool_call->name,
                    tool_args: $tool_call->arguments,
                    created_by_tool: true,
                );

                if ($this->logScheduler) {
                    ScheduleLogEvent::dispatch($project, $project->toArray());
                }

                $count++;
            }

            Log::info('This Tools Complete doing sending it through again');

            OrchestrateFacade::handle($project);
        }
    }
}
