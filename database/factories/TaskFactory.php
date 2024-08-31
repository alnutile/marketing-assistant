<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
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
            'details' => $this->faker->paragraph(),
            'completed' => false,
            'due_date' => $this->faker->date(),
            'assistant' => false,
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
        ];
    }
}
