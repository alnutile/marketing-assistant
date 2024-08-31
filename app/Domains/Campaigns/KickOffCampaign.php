<?php

namespace App\Domains\Campaigns;

use App\Models\Campaign;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use App\Services\LlmServices\RoleEnum;

class KickOffCampaign
{
    public function handle(Campaign $campaign)
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
