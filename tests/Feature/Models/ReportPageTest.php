<?php

namespace Tests\Feature\Models;

use App\Models\ReportPage;
use Tests\TestCase;

class ReportPageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $reportPage = ReportPage::factory()->create();
        $this->assertNotNull($reportPage->id);
        $this->assertNotNull($reportPage->report_id);
        $this->assertNotNull($reportPage->report->id);
        $this->assertNotNull($reportPage->content);
        $this->assertNotNull($reportPage->score);
        $this->assertNotNull($reportPage->status);
    }
}
