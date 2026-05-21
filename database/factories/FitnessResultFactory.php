<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FitnessResult>
 */
class FitnessResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participant_id' => null,
            'test_session_id' => null,
            'recorded_by' => null,
            'push_ups' => fake()->numberBetween(5, 70),
            'sit_ups' => fake()->numberBetween(8, 70),
            'sit_and_reach_cm' => fake()->randomFloat(2, 5, 50),
            'shuttle_run_level' => fake()->randomFloat(2, 4, 15),
            'run_2_4km_seconds' => fake()->numberBetween(620, 1400),
            'total_score' => 0,
            'classification' => 'Average',
            'result_status' => 'Pass',
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
