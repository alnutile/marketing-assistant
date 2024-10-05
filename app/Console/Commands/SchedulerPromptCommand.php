<?php

namespace App\Console\Commands;

use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SchedulerPromptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-prompt-command';

    protected array $jobs = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check every hour for prompts to schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * @TODO
         * Batch and limit so we don't overload the api
         */
        Project::active()->each(function (Project $project) {
            $this->jobs[] = new SchedulerProjectJob($project);
        });

        Bus::batch($this->jobs)
            ->name('Scheduler Project Jobs')
            ->allowFailures()
            ->onQueue('scheduler')
            ->dispatch();

    }
}
