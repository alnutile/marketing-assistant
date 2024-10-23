<?php

namespace App\Services\LlmServices\Orchestration;

use App\Events\ScheduleLogEvent;
use App\Models\Project;
use App\Services\Prism\Tools\CreateTask;
use App\Services\Prism\Tools\SendEmailToTeam;
use App\Services\Prism\Tools\TaskList;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\ValueObjects\Messages\AssistantMessage;
use EchoLabs\Prism\ValueObjects\Messages\UserMessage;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use EchoLabs\Prism\Facades\Tool;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate as OrchestrateFacade;
use Illuminate\Support\Facades\Log;

class PrismOrchestrate
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

        $messages = $project->getPrismMessage(limit: 10);

        $currentDateTime = sprintf('Current date and time: %s', now()->toDateTimeString());

        $systemPrompt = $project->system_prompt;

        $systemPrompt = <<<SYSTEM_PROMPT
## Current date and time: {$currentDateTime}

## System prompt:
$systemPrompt
SYSTEM_PROMPT;

        $prism = Prism::text()
            ->withSystemPrompt($systemPrompt)
            ->withMessages($messages)
            ->using('anthropic', 'claude-3-5-sonnet-latest')
            ->withPrompt("List out my open tasks")
            ->withTools([
                new TaskList($project),
                new CreateTask($project),
                new SendEmailToTeam($project),
            ])
            ->withMaxSteps(10)
            ->generate();

        foreach ($prism->responseMessages as $message) {
            if ($message instanceof AssistantMessage) {
                Log::info('Prism Assistant Message', [
                    'content' => $message->content(),
                ]);
            }
        }


//        $project->addInput(
//            message: $response->content,
//            role: RoleEnum::Assistant,
//        );
//
//        if ($this->logScheduler) {
//            ScheduleLogEvent::dispatch($project, $project->toArray());
//        }
//
//        Log::info('Orchestration Tools Found', [
//            'tool_calls' => count($response->tool_calls),
//        ]);
//
//        if (! empty($response->tool_calls)) {
//
//            Log::info('Orchestration Tools Found', [
//                'tool_calls' => collect($response->tool_calls)
//                    ->pluck('name')->toArray(),
//            ]);
//
//            $count = 1;
//            foreach ($response->tool_calls as $tool_call) {
//                Log::info('[LaraChain] - Tool Call '.$count, [
//                    'tool_call' => $tool_call->name,
//                    'tool_count' => count($response->tool_calls),
//                ]);
//
//                $tool = app()->make($tool_call->name);
//
//                $functionResponse = $tool->handle($project, $tool_call->arguments);
//
//                $project->addInput(
//                    message: sprintf('Tool %s used with results %s', $tool_call->name, $functionResponse->content),
//                    role: RoleEnum::User,
//                    tool_id: $tool_call->id,
//                    tool_name: $tool_call->name,
//                    tool_args: $tool_call->arguments,
//                    created_by_tool: true,
//                );
//
//                if ($this->logScheduler) {
//                    ScheduleLogEvent::dispatch($project, $project->toArray());
//                }
//
//                $count++;
//            }
//
//            Log::info('This Tools Complete doing sending it through again');
//
//            OrchestrateFacade::handle($project);
//        }
    }
}
