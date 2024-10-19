<?php

namespace Tests\Feature;

use App\Jobs\ReviewReportPageJob;
use App\Models\ReportPage;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Tests\TestCase;

class ReviewReportPageJobTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_job(): void
    {
        LlmDriverFacade::shouldReceive('driver->completion')
            ->twice()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'foo bar',
                ]),
                CompletionResponse::from([
                    'content' => '1',
                ])
            );

        $reportPage = ReportPage::factory()->create();

        [$job, $batch] = (new ReviewReportPageJob($reportPage))->withFakeBatch();

        $job->handle();

        $this->assertDatabaseCount('report_pages', 1);
    }
}
