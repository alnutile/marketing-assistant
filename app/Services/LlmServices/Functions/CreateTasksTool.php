<?php

namespace App\Services\LlmServices\Functions;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class CreateTasksTool extends FunctionContract
{
    protected string $name = 'create_tasks_tool';

    protected string $description = 'If the Project needs to have tasks created or the users prompt requires it you can use this tool to make multiple tasks';

    public function handle(
        Project $project,
        array $args = []): FunctionResponse
    {
        Log::info('TaskTool called');

        foreach (data_get($args, 'tasks', []) as $taskArg) {
            $name = data_get($taskArg, 'name', null);
            $details = data_get($taskArg, 'details', null);
            $due_date = data_get($taskArg, 'due_date', null);
            $assistant = data_get($taskArg, 'assistant', false);

            Task::updateOrCreate([
                'name' => $name,
                'project_id' => $project->id,
            ],
                [
                    'details' => $details,
                    'due_date' => $due_date,
                    'assistant' => $assistant
                ]);
        }

        return FunctionResponse::from([
            'content' => json_encode($args),
        ]);
    }

    /**
     * @return PropertyDto[]
     */
    protected function getProperties(): array
    {
        return [
            new PropertyDto(
                name: 'tasks',
                description: 'Array of task objects',
                type: 'array',
                required: true,
                properties: [
                    new PropertyDto(
                        name: 'items',
                        description: 'Task object',
                        type: 'object',
                        required: true,
                        properties: [
                            new PropertyDto(
                                name: 'name',
                                description: 'Name of the task',
                                type: 'string',
                                required: true
                            ),
                            new PropertyDto(
                                name: 'details',
                                description: 'Detailed info of the task',
                                type: 'string',
                                required: true
                            ),
                            new PropertyDto(
                                name: 'due_date',
                                description: 'Due date if any format "Y-m-d"',
                                type: 'string',
                                required: true
                            ),
                            new PropertyDto(
                                name: 'assistant',
                                description: 'Should the assistant be assigned this true or false',
                                type: 'string',
                            ),
                            new PropertyDto(
                                name: 'user_id',
                                description: 'User id if assigned to a user',
                                type: 'string',
                            ),
                        ]
                    ),
                ]
            ),
        ];
    }
}
