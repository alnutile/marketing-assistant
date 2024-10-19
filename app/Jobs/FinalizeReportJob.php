<?php

namespace App\Jobs;

use App\Models\Report;
use App\Services\LlmServices\LlmDriverFacade;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FinalizeReportJob implements ShouldQueue
{
    use Queueable, Batchable;

    //tries

    protected int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(public Report $report)
    {
        //
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        $report = $this->report;

        $count = $report->report_pages->count();

        $count = ($count < 8) ? $count : 8;
        $results = collect($report->report_pages->random($count))
            ->transform(function ($reportPage) {
                return <<<OUTPUT
      Review: {$reportPage->review}
      Score: {$reportPage->score}
OUTPUT;
            })
            ->implode("\n");

        $prompt = <<<PROMPT
This is a small selection of results form the review of the pages from the report.
Just make a final summary of the document review results based on the set you have below.

The finally summary should consider the score and overall review of the pages.

**example output**
Consider the following results of all the pages reviewed this document overall seems to be a good document.
There are a few pages that might need some work.

## Results from Review
$results
PROMPT;

        $results = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->completion($prompt);

        $this->report->updateQuietly([
            'summary_of_results' => $results->content,
            'status' => \App\Domains\Reports\StatusEnum::Completed,
            'overall_score' => $this->report->report_pages->sum('score'),
        ]);


        \Filament\Notifications\Notification::make()
            ->title('Report Completed')
            ->sendToDatabase($this->report->user);
    }
}
