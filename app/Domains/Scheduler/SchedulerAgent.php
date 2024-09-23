<?php

namespace App\Domains\Scheduler;
use App\Models\Project;
use App\Models\Task;
use EchoLabs\Sparkle\Facades\Agent;
use EchoLabs\Sparkle\Facades\Tool;
use Illuminate\Support\Facades\Log;

class SchedulerAgent
{

    protected Project $project;

    public function handle(Project $project, string $prompt)
    {
        $this->project = $project;

        return Agent::provider('openai')
            ->using('gpt-4')
            ->withOptions([
                'top_p' => 1,
                'temperature' => 0.8,
                'max_tokens' => 2048,
            ])
            ->withPrompt($prompt)
            ->withTools($this->tools());
    }

    /** @return array<int, Tool> */
    public function tools(): array
    {
        return [
            Tool::as('create_tasks_tool')
                ->for('If the Project needs to have tasks created or the users prompt requires it you can use this tool to make multiple tasks')
                ->withParameter('name', 'Name of the task', 'string', true)
                ->withParameter('details', 'Detailed info of the task', 'string', true)
                ->withParameter('due_date', 'Due date if any format "Y-m-d"', 'string', true)
                ->using(function (string $name, $details, $due_date): void {
                    Log::info('TaskTool called', [
                        'name' => $name,
                        'details' => $details,
                        'due_date' => $due_date,
                    ]);

                    Task::updateOrCreate([
                        'name' => $name,
                        'project_id' => $this->project->id,
                    ],
                        [
                            'details' => $details,
                            'due_date' => $due_date
                        ]);
                }),
        ];
    }
}
