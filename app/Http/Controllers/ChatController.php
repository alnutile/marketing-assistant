<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;

class ChatController extends Controller
{
    public function chat(Project $project)
    {
        $validated = request()->validate([
            'input' => 'required',
        ]);

        $project->update([
            'chat_status' => \App\Domains\Campaigns\ChatStatusEnum::InProgress->value,
        ]);

        Orchestrate::handle($project, $validated['input']);

        $project->update([
            'chat_status' => \App\Domains\Campaigns\ChatStatusEnum::Complete->value,
        ]);

        request()->session()->flash('flash.banner', 'Chat Complete');

        return back();
    }
}
