<?php

namespace App\Services\LlmServices\Orchestration;

use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate as OrchestrateFacade;
use Illuminate\Support\Facades\Log;

class Orchestrate
{
    public function handle(Project $project, string $prompt = '', RoleEnum $role = RoleEnum::User): void
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

        $currentDateTime = sprintf("Current date and time: %s", now()->toDateTimeString());

        $systemPrompt = <<<SYSTEM_PROMPT
Current date and time: {$currentDateTime}

System prompt:
$systemPrompt
SYSTEM_PROMPT;

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->setSystem($systemPrompt)
            ->chat($messages);

        //put_fixture('claude_response_before_tools_'.now()->timestamp.'.json', $response->toArray());

        $project->addInput(
            message: $response->content,
            role: RoleEnum::Assistant,
        );

        if (! empty($response->tool_calls)) {

            Log::info('Orchestration Tools Found', [
                'tool_calls' => collect($response->tool_calls)
                    ->pluck('name')->toArray(),
            ]);

            //put_fixture('claude_response_with_tools_'.now()->timestamp.'.json', $response->toArray());

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

                $count++;
            }

            Log::info('Tools Complete doing final chat');

            OrchestrateFacade::handle($project);
        }
    }
}
