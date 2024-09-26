<?php

namespace App\Domains\Campaigns;

use App\Models\Project;
use App\Models\Task;
use App\Notifications\DailyReport;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DailyReportService
{
    public function handle(): void
    {
        foreach (Project::active()->get() as $project) {
            $this->sendReport($project);
        }
    }

    public function sendReport(Project $project)
    {
        $tasks = Task::where('project_id', $project->id)
            ->notCompleted()
            ->where('due_date', '>=', now()->addDays(7))
            ->get()
            /** @phpstan-ignore-next-line */
            ->transform(function (Task $item) {
                return sprintf(
                    'Task: %s %s %s',
                    $item->name,
                    $item->details,
                    $item->due_date
                );
            })->implode(', ');

        $prompt = CampaignDailyReportPrompt::getPrompt($project, $tasks);

        $project->addInput(
            message: $prompt,
            role: RoleEnum::User,
            user: $project->user,
        );

        $messages = $project->getMessageThread();

        $results = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->chat($messages);

        $project->addInput(
            message: $results->content,
            role: RoleEnum::Assistant,
            user: null,
        );

        Log::info('DailyReportService::handle', [
            'results' => $results->content,
        ]);

        foreach($project->team->users as $user) {
            Notification::send($user, new DailyReport($results->content, $project));
        }
    }
}
