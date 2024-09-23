<?php

namespace App\Domains\Scheduler;

use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Requests\MessageInDto;
use App\Services\LlmServices\RoleEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OrchestrateScheduler
{
    protected Collection $messages;

    public function handle(Project $project) : void
    {
        Log::info('Scheduler called');
        //see about completed tasks
        //see about past tasks
        //see about goals
        //see about the scheduler prompt itself
        //see if actions from that
        //sse if actions based on main prompt
        //make sure to include time and day
        $schedulerPrompt = $project->scheduler_prompt;
        $systemPrompt = $project->system_prompt;

        $this->messages = collect([
            MessageInDto::from([
                'content' => $systemPrompt,
                'role' => RoleEnum::System->value,
            ]),
            MessageInDto::from([
                'content' => $schedulerPrompt,
                'role' => RoleEnum::User->value,
            ]),
        ]);

        Log::info('Messages', [
            'messages' => $this->messages->toArray(),
        ]);

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->chat($this->messages->reverse());

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
                $tool->handle($project, $tool_call->arguments);

                $message = $project->addInput(
                    message: sprintf('Tool %s complete', $tool_call->name),
                    role: RoleEnum::Tool,
                    tool_id: $tool_call->id,
                    tool_name: $tool_call->name,
                    tool_args: $tool_call->arguments,
                );

                $this->messages->push(MessageInDto::from([
                    'content' => $message,
                    'tool' => $tool_call->name,
                    'tool_id' => $tool_call->id,
                    'args' => $tool_call->arguments,
                    'role' => RoleEnum::Assistant->value,
                ]));

                $count++;
            }

            Log::info('Tools Complete doing final chat');

            put_fixture("claude_error_final_messages_v2.json", $this->messages->toArray());
            /**
             * @NOTE
             * I have to continue to pass in tools once used above
             * Since Claude needs them.
             */
            $response = LlmDriverFacade::driver(config('llmdriver.driver'))
                ->chat($this->messages);

            $project->addInput(
                message: $response->content,
                role: RoleEnum::Assistant,
            );

        } else {
            Log::info('[LaraChain] - No Tools found just gonna chat');
        }
    }
}
