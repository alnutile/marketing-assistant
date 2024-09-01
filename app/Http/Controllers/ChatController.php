<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;

class ChatController extends Controller
{
    public function chat(Campaign $campaign)
    {
        $validated = request()->validate([
            'input' => 'required',
        ]);

        $campaign->update([
            'chat_status' => \App\Domains\Campaigns\ChatStatusEnum::InProgress->value,
        ]);

        Orchestrate::handle($campaign, $validated['input']);

        $campaign->update([
            'chat_status' => \App\Domains\Campaigns\ChatStatusEnum::Complete->value,
        ]);

        request()->session()->flash('flash.banner', 'Chat Complete');

        return back();

    }
}
