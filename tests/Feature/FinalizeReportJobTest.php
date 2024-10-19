<?php

namespace Tests\Feature;

use App\Jobs\FinalizeReportJob;
use App\Models\Report;
use App\Models\ReportPage;
use App\Services\LlmServices\LlmDriverFacade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FinalizeReportJobTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_job(): void
    {
        $report = Report::factory()->create();

        ReportPage::factory(3)->create([
            'report_id' => $report->id,
        ]);

        LlmDriverFacade::shouldReceive('driver->completion')
            ->once()
            ->andReturn(
                \App\Services\LlmServices\Responses\CompletionResponse::from([
                    'content' => 'Test Content',
                ])
            );

        [$job, $batch] = (new FinalizeReportJob($report))->withFakeBatch();
        $job->handle();

        $report = $report->fresh();
        $this->assertEquals('Test Content', $report->summary_of_results);
        $this->assertEquals(\App\Domains\Reports\StatusEnum::Completed, $report->status);
    }
}
