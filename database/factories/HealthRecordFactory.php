<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthRecord>
 */
class HealthRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $height = fake()->randomFloat(2, 150, 190);
        $weight = fake()->randomFloat(2, 45, 110);
        $bmi = round($weight / (($height / 100) * ($height / 100)), 2);

        return [
            'participant_id' => null,
            'test_session_id' => null,
            'recorded_by' => null,
            'height_cm' => $height,
            'weight_kg' => $weight,
            'bmi' => $bmi,
            'blood_pressure_systolic' => fake()->numberBetween(105, 145),
            'blood_pressure_diastolic' => fake()->numberBetween(65, 95),
            'glucose_mmol' => fake()->randomFloat(2, 3.5, 8.5),
            'cholesterol_mmol' => fake()->randomFloat(2, 3.0, 8.0),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
