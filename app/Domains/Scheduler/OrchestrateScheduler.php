<?php

namespace App\Domains\Scheduler;

use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate as OrchestrateFacade;
use Illuminate\Support\Facades\Log;

class OrchestrateScheduler
{
    public function handle(Project $project): void
    {
        Log::info('Scheduler called');

        $schedulerPrompt = $project->scheduler_prompt;
        $systemPrompt = $project->system_prompt;

        $project->addInput(
            message: $schedulerPrompt,
            role: RoleEnum::User,
            user: auth()->user());

        $messages = $project->getMessageThread();

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->setSystem($systemPrompt)
            ->chat($messages);

        if (! empty($response->tool_calls)) {
            Log::info('Orchestration Tools Found', [
                'tool_calls' => collect($response->tool_calls)
                    ->pluck('name')->toArray(),
            ]);

            put_fixture('response_with_tools_'.now()->timestamp.'.json', $response->toArray());

            $count = 1;
            foreach ($response->tool_calls as $tool_call) {
                Log::info('[LaraChain] - Tool Call '.$count, [
                    'tool_call' => $tool_call->name,
                    'tool_count' => count($response->tool_calls),
                ]);

                $tool = app()->make($tool_call->name);
                $functionResponse = $tool->handle($project, $tool_call->arguments);

                $project->addInput(
                    message: sprintf('Tool %s complete', $tool_call->name),
                    role: RoleEnum::Tool,
                    tool_id: $tool_call->id,
                    tool_name: $tool_call->name,
                    tool_args: $tool_call->arguments,
                );

                $count++;
            }

            Log::info('Tools Complete doing final chat');

            OrchestrateFacade::handle($project, $functionResponse->content);

        } else {
            Log::info('[LaraChain] - No Tools found just gonna chat');
        }
    }
}
