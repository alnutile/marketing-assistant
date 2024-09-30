<?php

namespace App\Jobs;

use App\Models\Automation;
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
    public function __construct(Automation $automation, string $payload)
    {
        if ($this->batch()?->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }

        $automation->run($payload);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
