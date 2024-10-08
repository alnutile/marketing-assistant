<?php

namespace Tests\Feature\Http\Controllers;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $user->id,
        ]);

        $team->users()->attach($user, ['role' => 'admin']);

        $user->current_team_id = $team->id;
        $user->updateQuietly();

        Project::factory(3)->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $teamNot = Team::factory()->create();
        Project::factory()->create([
            'team_id' => $teamNot->id,
        ]);

        $this->actingAs($user)->get(
            route('projects.index')
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('projects.data', 3)
            );
    }

    public function test_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(
            route('projects.create')
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('statuses')
                ->has('productServices')
            );
    }

    public function test_store(): void
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->current_team_id = $team->id;
        $user->updateQuietly();

        $this->actingAs($user)->post(
            route('projects.store'), [
                'name' => 'Test Project',
                'start_date' => '2023-01-01',
                'end_date' => '2023-01-01',
                'content' => 'Test Description',
                'system_prompt' => 'Test System Prompt',
                'scheduler_prompt' => 'Test Scheduler Prompt',
                'status' => StatusEnum::Draft->value,
                'product_or_service' => ProductServiceEnum::PhysicalProduct->value,
                'target_audience' => 'Test Audience',
                'budget' => '1000',
            ]
        )
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $project = Project::first();

        $this->assertEquals(
            ChatStatusEnum::Pending->value,
            $project->chat_status->value
        );

        $this->assertNotNull($project->user_id);
        $this->assertNotNull($project->team_id);

    }

    public function test_show(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(
            route('projects.show', [
                'project' => $project->id,
            ])
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('project.data')
            );
    }

    public function test_edit(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(
            route('projects.edit', $project)
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('project.data')
            );
    }

    public function test_update(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->put(
            route('projects.update', $project)
        )->assertStatus(302);
    }

    public function test_destroy(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->delete(
            route('projects.destroy', $project)
        )->assertStatus(302);

        $this->assertDatabaseCount('projects', 0);
    }
}
