<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AutomationRunnerJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function onQueue($queue): string
    {
        return 'automations';
    }

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
