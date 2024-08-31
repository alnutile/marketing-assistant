<?php

namespace Tests\Feature;

use App\Domains\Campaigns\KickOffCampaign;
use App\Models\Campaign;
use App\Models\User;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
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

        Orchestrate::shouldReceive('handle')->once();

        (new KickOffCampaign)->handle($campaign);

    }
}
