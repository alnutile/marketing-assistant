<?php

namespace App\Services\LlmServices\Orchestration;

use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Illuminate\Support\Facades\Log;

class Orchestrate
{
    public function handle(Project $campaign, string $prompt): void
    {
        $campaign->addInput(
            message: $prompt,
            role: RoleEnum::User,
            user: auth()->user());

        $messages = $campaign->getMessageThread();

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
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
                $tool->handle($campaign, $tool_call->arguments);

                $campaign->addInput(
                    message: sprintf('Tool %s complete', $tool_call->name),
                    role: RoleEnum::Tool,
                    tool_id: $tool_call->id,
                    tool_name: $tool_call->name,
                    tool_args: $tool_call->arguments,
                );

                $count++;
            }

            Log::info('Tools Complete doing final chat');

            $messages = $campaign->getMessageThread();

            /**
             * @NOTE
             * I have to continue to pass in tools once used above
             * Since Claude needs them.
             */
            $response = LlmDriverFacade::driver(config('llmdriver.driver'))
                ->chat($messages);

            $campaign->addInput(
                message: $response->content,
                role: RoleEnum::Assistant,
            );

        } else {
            Log::info('[LaraChain] - No Tools found just gonna chat');
            $campaign->addInput(
                message: $response->content ?? 'Calling Tools', //ollama, openai blank but claude needs this :(
                role: RoleEnum::Assistant
            );
        }
    }
}
