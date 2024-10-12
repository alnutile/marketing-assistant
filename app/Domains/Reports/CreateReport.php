<?php

namespace App\Domains\Reports;

use App\Models\Project;
use App\Models\Report;

class CreateReport
{
    public function handle(
        Report $report
    ): void {
        //I need a file path
        //I need a project so I can save the report to?
        //Reports have types this one is type: Standards Checker

        //break the PDF into pages
        //Run a review of each page
        //Save results to the ReportPage with a score
    }
}
