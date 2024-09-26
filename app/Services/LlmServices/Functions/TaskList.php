<?php

namespace App\Services\LlmServices\Functions;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskList extends FunctionContract
{
    protected string $name = 'list_tasks';

    protected string $description = 'This will list all tasks that are still open unless you request closed instead';

    public function handle(
        Project $project,
        array $args = []): FunctionResponse
    {
        Log::info('List Tasks called');

        $state = data_get($args, 'state', 'open');

        $tasks = Task::where('project_id', $project->id)
            ->when($state === 'closed', function ($query) {
                return $query->where('completed', true);
            })
            ->get();

        $formattedTasks = $tasks->map(function ($task) {
            return sprintf(
                "Task Name: %s\n" .
                "Task Id: %d\n" .
                "Due: %s\n" .
                "Completed At: %s\n" .
                "Details: \n%s\n",
                $task->name,
                $task->id,
                $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A',
                $task->completed ? $task->completed->format('Y-m-d') : 'null',
                $task->details
            );
        })->join("\n");

        return FunctionResponse::from([
            'content' => $formattedTasks,
        ]);
    }

    /**
     * @return PropertyDto[]
     */
    protected function getProperties(): array
    {
        return [
            new PropertyDto(
                name: 'state',
                description: 'open or closed will be open by default',
                type: 'string',
                required: true,
            ),
        ];
    }
}
