<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-report-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily report to campaign users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    }
}
