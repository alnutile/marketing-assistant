<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
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

        //        $campaign->addInput(
        //            message: $validated['input'],
        //            role: RoleEnum::User,
        //            user: auth()->user(),
        //        );

        Orchestrate::handle($campaign, $validated['input']);

        //        $messages = $campaign->getMessageThread();
        //
        //        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
        //            ->chat($messages);
        //
        //        $campaign->addInput(
        //            message: $response->content,
        //            role: RoleEnum::Assistant,
        //            user: auth()->user(),
        //        );

        $campaign->update([
            'chat_status' => \App\Domains\Campaigns\ChatStatusEnum::Complete->value,
        ]);

        request()->session()->flash('flash.banner', 'Chat Complete');

        return back();

    }
}
