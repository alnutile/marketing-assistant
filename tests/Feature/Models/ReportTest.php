<?php

namespace Tests\Feature\Models;

use App\Models\Report;
use Tests\TestCase;

class ReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $report = Report::factory()->create();
        $this->assertNotNull($report->id);
        $this->assertNotNull($report->summary_of_results);
        $this->assertNotNull($report->prompt);
        $this->assertNotNull($report->overall_score);
        $this->assertNotNull($report->report_type);
        $this->assertNotNull($report->status);
        $this->assertNotNull($report->project->id);
    }
}
