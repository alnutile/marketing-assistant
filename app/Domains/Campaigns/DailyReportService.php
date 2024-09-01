<?php

namespace App\Domains\Campaigns;

use App\Models\Campaign;
use App\Models\Task;
use App\Notifications\DailyReport;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DailyReportService
{

    public function handle() : void
    {
        foreach(Campaign::active()->get() as $campaign) {
            $tasks = Task::where('campaign_id', $campaign->id)
                ->notCompleted()
                ->where('due_date', '>=', now()->addDays(7))
                ->get()
                ->transform(function ($item) {
                    return sprintf(
                        'Task: %s %s %s',
                        $item->name,
                        $item->details,
                        $item->due_date
                    );
                })->implode(', ');

            $prompt = CampaignDailyReportPrompt::getPrompt($campaign, $tasks);

            $campaign->addInput(
                message: $prompt,
                role: RoleEnum::User,
                user: $campaign->user,
            );

            $messages = $campaign->getMessageThread();

            $results = LlmDriverFacade::driver(config('llmdriver.driver'))
                ->chat($messages);

            $campaign->addInput(
                message: $results->content,
                role: RoleEnum::Assistant,
                user: null,
            );

            Log::info('DailyReportService::handle', [
                'results' => $results->content,
            ]);

            Notification::send($campaign->user, new DailyReport($results->content, $campaign));

        }
    }
}
