<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Services\LlmServices\Orchestration\PrismOrchestrate;
use Tests\TestCase;

class PrismOrchestrateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_prism_tool(): void
    {
        $project = Project::factory()->create();

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'completed_at' => null,
            'details' => 'Go food shopping',
        ]);

        (new PrismOrchestrate)->handle($project);
    }
}
