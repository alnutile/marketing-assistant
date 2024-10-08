<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ScheduleLog;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SchedulerProjectJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Project $project)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $prompt = $this->project->scheduler_prompt;
        $dateTime = now()->toDateTimeString();

        $logs = ScheduleLog::recentLogs($this->project);

        $prompt = <<<PROMPT
# Current day and time:
$dateTime

# Scheduler Prompt:
$prompt

# Recent Scheduler Logs for this project:
$logs
PROMPT;
        Orchestrate::setLogScheduler(true)
            ->handle($this->project, $prompt);

    }
}
