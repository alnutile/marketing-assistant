<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Campaign;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Campaign $campaign)
    {
        $tasks = TaskResource::collection($campaign->tasks()->orderBy("due_date", "asc")->notCompleted()->get());
        return response()->json([
            "tasks" => $tasks
        ]);
    }

    public function markAsComplete(Request $request, Task $task)
    {
        $task->update([
            'completed_at' => now()
        ]);

        request()->session()->flash('flash.banner', 'Completed');

        return back();
    }
}
