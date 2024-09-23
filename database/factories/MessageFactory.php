<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph(),
            'role' => $this->faker->randomElement(['assistant', 'user']),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'tool_id' => Str::random(32),
            'tool_name' => 'create_tasks_tool',
            'tool_args' => [
                'tasks' => [
                    [
                        'name' => $this->faker->name(),
                        'details' => $this->faker->paragraph(),
                        'completed' => false,
                        'due_date' => $this->faker->date(),
                        'assistant' => false,
                    ],
                ],
            ],
        ];
    }
}
