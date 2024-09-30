<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Orchestration\Orchestrate;
use App\Services\LlmServices\Responses\CompletionResponse;
use Tests\TestCase;

class OrchestrateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_tools(): void
    {
        $response1 = get_fixture('claude_response_with_tools_1727478177.json');
        $response2 = get_fixture('claude_response_with_tools_1727478179.json');
        $response3 = get_fixture('claude_response_with_tools_1727478189.json');
        $response4 = get_fixture('claude_response_before_tools_1727478194.json');

        $project = Project::factory()->create();

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('tasks', 0);

        LlmDriverFacade::shouldReceive('driver->setSystem->chat')
            ->times(4)
            ->andReturn(
                CompletionResponse::from($response1),
                CompletionResponse::from($response2),
                CompletionResponse::from($response3),
                CompletionResponse::from($response4),
            );

        (new Orchestrate)->handle($project, 'Test Prompt');

        $this->assertDatabaseCount('messages', 8);
        $this->assertDatabaseCount('tasks', 7);
    }
}
