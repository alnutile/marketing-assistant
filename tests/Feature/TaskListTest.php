<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Services\Prism\Tools\TaskList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskListTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_tool(): void
    {
        $project = Project::factory()->create();

        Task::factory(3)->create([
            'project_id' => $project->id,
            'completed_at' => null,
        ]);

        $results = (new TaskList($project))('open');

        $this->assertNotNull($results);

        $this->assertStringContainsString(Task::first()->name, $results);
    }
}
