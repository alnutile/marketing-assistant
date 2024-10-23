<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Services\Prism\Tools\CreateTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_tool(): void
    {
        $project = Project::factory()->create();

        $tasks = [
            [
                'name' => 'Test Task 1',
                'details' => 'Test Details 1',
                'due_date' => '2024-10-01',
                'assistant' => false,
            ],
            [
                'name' => 'Test Task 2',
                'details' => 'Test Details 2',
                'due_date' => '2024-10-02',
                'assistant' => true,
            ],
        ];

        $this->assertDatabaseCount('tasks', 0);

        $results = (new CreateTask($project))($tasks);

        $this->assertDatabaseCount('tasks', 2);

        $this->assertStringContainsString('Test Task 1', $results);
        $this->assertStringContainsString('Test Task 2', $results);
    }
}
