<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestSession>
 */
class TestSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_code' => 'UKJK-'.fake()->unique()->numerify('####'),
            'title' => 'UKJK Fitness Assessment '.fake()->randomElement(['Zone A', 'Zone B', 'Zone C']),
            'location' => fake()->randomElement(['Kompleks Sukan JPJ', 'Padang KKM', 'Stadium Negeri']),
            'session_date' => fake()->dateTimeBetween('-2 months', '+2 months')->format('Y-m-d'),
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
            'status' => fake()->randomElement(['scheduled', 'ongoing', 'completed']),
            'description' => fake()->sentence(12),
            'created_by' => null,
        ];
    }
}
