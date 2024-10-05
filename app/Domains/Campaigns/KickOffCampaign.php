<?php

namespace App\Domains\Campaigns;

use App\Models\Project;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;

class KickOffCampaign
{
    public function handle(Project $project)
    {
        $project->updateQuietly([
            'chat_status' => ChatStatusEnum::InProgress,
        ]);

        $project->messages()->delete();

        $project->tasks()->delete();

        $projectContext = $project->getContext();

        $prompt = CampaignKickOffPrompt::getPrompt($projectContext);

        Orchestrate::handle($project, $prompt);

        $project->updateQuietly([
            'chat_status' => ChatStatusEnum::Complete,
        ]);
    }
}
