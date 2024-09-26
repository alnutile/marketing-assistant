<?php

namespace Tests\Feature;

use App\Domains\Scheduler\OrchestrateScheduler;
use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Tests\TestCase;

class OrchestrateSchedulerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_runs(): void
    {
        LlmDriverFacade::shouldReceive('driver->chat')
            ->once()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'Test Content',
                ])
            );

        $project = Project::factory()->create();

        (new OrchestrateScheduler)->handle($project);
    }
}
