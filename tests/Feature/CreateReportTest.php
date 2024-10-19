<?php

namespace Tests\Feature;

use App\Domains\Reports\CreateReport;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_breaks_up_pdf(): void
    {
        Bus::fake();
        Storage::disk("reports")->copy(base_path('tests/example-documents/MockRFP.pdf'), 'MockRFP.pdf');

        $report = Report::factory()->create([
            'file_name' => 'MockRFP.pdf',
        ]);

        (new CreateReport())->handle($report);
        $this->assertDatabaseCount('report_pages', 3);

        Bus::assertBatchCount(1);
    }
}
