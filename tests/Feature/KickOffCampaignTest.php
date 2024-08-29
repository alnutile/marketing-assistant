<?php

namespace Tests\Feature;

use App\Domains\Campaigns\KickOffCampaign;
use App\Models\Campaign;
use App\Models\User;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Tests\TestCase;

class KickOffCampaignTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_kickoff(): void
    {
        $campaign = Campaign::factory()->create();

        $user = User::factory()->create();

        $this->actingAs($user);

        LlmDriverFacade::shouldReceive('driver->completion')
            ->once()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'Hello world! um just reply with hello back',
                ])
            );

        $this->assertDatabaseCount('messages', 0);

        (new KickOffCampaign)->handle($campaign);

        $this->assertDatabaseCount('messages', 3);

    }
}
