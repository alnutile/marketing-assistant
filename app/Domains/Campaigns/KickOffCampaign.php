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
        $systemPrompt = $project->system_prompt;
        $now = now()->toDateTimeString();

        $prompt = <<<PROMPT
Date and time: $now

We are kicking off this project please use the prompts below to get it started as needed.

## System Prompt
$systemPrompt

## Context Prompt
$projectContext

PROMPT;

        Orchestrate::handle($project, $prompt);

        $project->updateQuietly([
            'chat_status' => ChatStatusEnum::Complete,
        ]);
    }
}
