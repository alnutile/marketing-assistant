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

        $systemPrompt = $project->system_prompt;

        $messages = $project->getMessageThread();

        $currentDateTime = sprintf("Current date and time: %s", now()->toDateTimeString());

        $systemPrompt = <<<SYSTEM_PROMPT
Current date and time: {$currentDateTime}

System prompt:
$systemPrompt
SYSTEM_PROMPT;

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->setSystem($systemPrompt)
            ->chat($messages);

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

                /**
                 * If I do this alone the loop never ends
                 * @see https://docs.anthropic.com/en/docs/build-with-claude/tool-use#example-of-successful-tool-result
                 */
                $project->addInput(
                    message: $functionResponse->content,
                    role: RoleEnum::User,
                    tool_id: $tool_call->id,
                    tool_name: $tool_call->name,
                    tool_args: $tool_call->arguments,
                );

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

            OrchestrateFacade::handle($project);

        } else {
            Log::info('[LaraChain] - No Tools found just gonna chat');
            $project->addInput(
                message: $response->content ?? 'Calling Tools', //ollama, openai blank but claude needs this :(
                role: RoleEnum::Assistant
            );
        }
    }
}
