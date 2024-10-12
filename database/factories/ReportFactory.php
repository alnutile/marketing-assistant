<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'report_type' => \App\Domains\Reports\ReportTypes::StandardsChecking->value,
            'summary_of_results' => $this->faker->sentence(3, true),
            'prompt' => $this->faker->sentence(3, true),
            'overall_score' => random_int(1, 5),
            'status' => \App\Domains\Reports\StatusEnum::Pending->value,
        ];
    }
}
