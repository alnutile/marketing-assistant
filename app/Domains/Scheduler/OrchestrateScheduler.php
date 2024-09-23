<?php

namespace App\Domains\Scheduler;

use App\Models\Project;

class OrchestrateScheduler
{
    public function handle(Project $project)
    {
        //see about completed tasks
        //see about past tasks
        //see about goals
        //see about the scheduler prompt itself
        //see if actions from that
        //sse if actions based on main prompt
        //make sure to include time and day
        $prompt = $project->scheduler_prompt;
        $systemPrompt = $project->system_prompt;



    }
}
