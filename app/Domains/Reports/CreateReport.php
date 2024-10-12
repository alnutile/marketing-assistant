<?php

namespace App\Domains\Reports;

use App\Models\Project;
use App\Models\Report;
use App\Models\ReportPage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class CreateReport
{
    public function handle(
        Report $report
    ): void {
        $filePath = storage_path('app/reports/' . $report->file_name);
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $pages = $pdf->getPages();
        $reportPages = [];

        foreach ($pages as $page_number => $page) {
            try {
                $page_number = $page_number + 1;
                $pageContent = $page->getText();
                $reportPages[] = ReportPage::create([
                    'report_id' => $report->id,
                    'sort' => $page_number,
                    'content' => $pageContent,
                    'score' => 0,
                ]);
            } catch (\Exception $e) {
                Log::error('Error parsing PDF', ['error' => $e->getMessage()]);
            }
        }

        //Run a review of each page
        //Save results to the ReportPage with a score
    }
}
