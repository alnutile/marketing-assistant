<?php

namespace App\Domains\Campaigns;

use App\Models\Project;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;

class KickOffCampaign
{
    public function handle(Project $campaign)
    {
        $campaign->updateQuietly([
            'chat_status' => ChatStatusEnum::InProgress,
        ]);

        $campaign->messages()->delete();

        $campaign->tasks()->delete();

        $campaignContext = $campaign->getContext();

        $prompt = CampaignKickOffPrompt::getPrompt($campaignContext);

        Orchestrate::handle($campaign, $prompt);

        $campaign->updateQuietly([
            'chat_status' => ChatStatusEnum::Complete,
        ]);
    }
}
