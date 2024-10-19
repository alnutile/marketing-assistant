<?php

namespace App\Domains\Reports;

use App\Jobs\FinalizeReportJob;
use App\Jobs\ReviewReportPageJob;
use App\Models\Report;
use App\Models\ReportPage;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class CreateReport
{
    public function handle(
        Report $report
    ): void {

        $report->report_pages()->delete();

        $filePath = storage_path('app/reports/'.$report->file_name);
        $parser = new Parser;
        $pdf = $parser->parseFile($filePath);
        $pages = $pdf->getPages();
        $reportPages = [];

        foreach ($pages as $page_number => $page) {
            try {
                $page_number = $page_number + 1;
                $pageContent = $page->getText();
                $reportPage = ReportPage::create([
                    'report_id' => $report->id,
                    'sort' => $page_number,
                    'content' => $pageContent,
                    'score' => 0,
                ]);
                $reportPages[] = new ReviewReportPageJob($reportPage);
            } catch (\Exception $e) {
                Log::error('Error parsing PDF', ['error' => $e->getMessage()]);
            }
        }

        Bus::batch($reportPages)
            ->name('Report Pages')
            ->allowFailures()
            ->allowFailures()
            ->finally(function (Batch $batch) use ($report) {
                $batch->add(new FinalizeReportJob($report));
                \Filament\Notifications\Notification::make()
                    ->title('Working on final parts of report')
                    ->sendToDatabase($report->user);
                //@TODO trigger a job to wrap up the report

            })
            ->dispatch();
    }
}
