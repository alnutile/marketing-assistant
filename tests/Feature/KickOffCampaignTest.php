<?php

namespace Tests\Feature;

use App\Domains\Campaigns\KickOffCampaign;
use App\Models\Project;
use App\Models\User;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Tests\TestCase;

class KickOffCampaignTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_kickoff(): void
    {
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $this->actingAs($user);

        Orchestrate::shouldReceive('handle')->once();

        (new KickOffCampaign)->handle($project);

    }
}
