<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Automation>
 */
class AutomationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'prompt' => $this->faker->paragraph(),
            'slug' => $this->faker->slug(),
            'enabled' => true,
            'scheduled' => false,
            'user_id' => User::factory(),
            'feedback_required' => false,
            'project_id' => Project::factory(),

        ];
    }
}
