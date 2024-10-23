<?php

namespace App\Services\Prism\Tools;

use App\Models\Project;
use App\Models\Task;
use EchoLabs\Prism\Schema\ArraySchema;
use EchoLabs\Prism\Schema\ObjectSchema;
use EchoLabs\Prism\Schema\StringSchema;
use EchoLabs\Prism\Tool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateTask extends Tool
{

    public function __construct(public Project $project)
    {
        $taskSchema = new ObjectSchema(
            name: 'task',
            description: 'Task object',
            properties: [
                'name' => new StringSchema(
                    name: 'name',
                    description: 'Name of the task',
                ),
                'details' => new StringSchema(
                    name: 'details',
                    description: 'Detailed info of the task',
                ),
                'due_date' => new StringSchema(
                    name: 'due_date',
                    description: 'Due date if any format "Y-m-d"',
                ),
                'assistant' => new StringSchema(
                    name: 'assistant',
                    description: 'Should the assistant be assigned this true or false',
                ),
                'user_id' => new StringSchema(
                    name: 'user_id',
                    description: 'User id if assigned to a user',
                ),
            ],
            requiredFields: ['name', 'details'],
        );

        $tasksParameter = new ArraySchema(
            name: 'tasks',
            description: 'an array of tasks objects',
            item: $taskSchema,
        );

        $this
            ->as('create_tasks_tool')
            ->for('If the Project needs to have tasks created or the users prompt requires it you can use this tool to make multiple tasks')
            ->withParameter($tasksParameter)
            ->using($this);
    }

    public function __invoke(array $tasks): string
    {

        Log::info('PrismOrchestrate::create_tasks_tool called');

        $tasksCreated = collect();

        foreach ($tasks as $task) {

            $tasksCreated->add(Task::updateOrCreate([
                    'name' => $task['name'],
                    'project_id' => $this->project->id,
                ],
                [
                    'details' => $task['details'],
                    'due_date' => data_get($task, 'due_date'),
                    'assistant' => data_get($task, 'assistant', false),
                ])
            );
        }

        return $tasksCreated
            ->map(function ($task) {
                return sprintf(
                    "Task Name: %s\n".
                    "Task Id: %d\n".
                    "Due: %s\n".
                    "Completed At: %s\n".
                    "Details: \n%s\n",
                    $task->name,
                    $task->id,
                    $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A',
                    $task->completed_at ? $task->completed_at->format('Y-m-d') : 'null',
                    $task->details
                );
            })
            ->implode("\n");
    }

}
