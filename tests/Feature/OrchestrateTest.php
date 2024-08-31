<?php

namespace Tests\Feature;

use App\Models\Campaign;
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
        $response = get_fixture('claude_chat_response.json', false);

        $campaign = Campaign::factory()->create();

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('tasks', 0);

        LlmDriverFacade::shouldReceive('driver->chat')
            ->twice()
            ->andReturn(
                CompletionResponse::from($response)
            );

        (new Orchestrate)->handle($campaign, 'Test Prompt');

        $this->assertDatabaseCount('messages', 3);
        $this->assertDatabaseCount('tasks', 5);
    }
}
