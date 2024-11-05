<?php

namespace App\Services\Prism\Tools;

use App\Models\Project;
use App\Models\Task;
use EchoLabs\Prism\Tool;
use Illuminate\Support\Facades\Log;

class TaskList extends Tool
{
    public function __construct(public Project $project)
    {
        $this
            ->as('list_tasks')
            ->for('This will list all tasks that are still open unless you request closed instead')
            ->withStringParameter(name: 'state', description: 'open or closed will be open by default', required: false)
            ->using($this);
    }

    public function __invoke(string $state): string
    {
        Log::info('PrismOrchestrate::list_tasks called');

        $tasks = Task::where('project_id', $this->project->id)
            ->when($state === 'closed', function ($query) {
                return $query->whereNotNull('completed_at');
            })
            ->get()
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
            })->join("\n");

        return $tasks;

    }
}
