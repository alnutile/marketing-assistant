<?php

namespace App\Domains\Campaigns;

use App\Models\Campaign;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\RoleEnum;

class KickOffCampaign
{
    public function handle(Campaign $campaign)
    {
        $campaign->updateQuietly([
            'chat_status' => ChatStatusEnum::InProgress,
        ]);

        $campaign->messages()->delete();

        $campaignContext = $campaign->getContext();

        $prompt = CampaignKickOffPrompt::getPrompt($campaignContext);

        $campaign->addInput(
            $prompt,
            RoleEnum::User,
            CampaignSystemPrompt::handle(),
            auth()->user());

        $response = LlmDriverFacade::driver(config('llmdriver.driver'))
            ->completion($prompt);

        $campaign->addInput(
            $response->content,
            RoleEnum::Assistant,
            CampaignSystemPrompt::handle()
        );

        $campaign->updateQuietly([
            'chat_status' => ChatStatusEnum::Complete,
        ]);
    }
}
