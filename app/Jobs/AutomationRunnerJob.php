<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutomationRunnerJob implements ShouldQueue
{
    use Queueable, Batchable;

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
