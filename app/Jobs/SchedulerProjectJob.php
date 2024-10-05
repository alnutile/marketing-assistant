<?php

namespace App\Jobs;

use App\Models\Project;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SchedulerProjectJob implements ShouldQueue
{
    use Queueable, Batchable;

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

        $prompt = <<<PROMPT
Current day and time: $dateTime

Scheduler Prompt:
$prompt
PROMPT;

        Orchestrate::setLogScheduler(true)->handle($this->project, $prompt);

    }
}
