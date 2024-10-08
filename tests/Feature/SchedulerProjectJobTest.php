<?php

namespace Tests\Feature;

use App\Domains\Campaigns\StatusEnum;
use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Tests\TestCase;

class SchedulerProjectJobTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_gets_logs(): void
    {

        Orchestrate::shouldReceive('setLogScheduler->handle')->once();

        $project = Project::factory()->create([
            'status' => StatusEnum::Active,
        ]);

        [$job, $batch] = (new SchedulerProjectJob($project))->withFakeBatch();

        $job->handle();

    }
}
