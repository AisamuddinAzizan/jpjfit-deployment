<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participant_no' => 'JPJ-'.fake()->unique()->numerify('#####'),
            'full_name' => fake()->name(),
            'ic_no' => fake()->unique()->numerify('############'),
            'date_of_birth' => fake()->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '01'.fake()->numerify('########'),
            'agency' => fake()->randomElement(['JPJ Putrajaya', 'JPJ Selangor', 'JPJ Johor', 'JPJ Perak']),
            'rank' => fake()->randomElement(['Pegawai', 'Penolong Pegawai', 'Pembantu']),
            'address' => fake()->address(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => '01'.fake()->numerify('########'),
            'is_active' => true,
        ];
    }
}
