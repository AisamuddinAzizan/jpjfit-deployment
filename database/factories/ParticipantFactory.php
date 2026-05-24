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
            'participant_no' => 'JPJ-'.$this->faker->unique()->numerify('#####'),
            'name' => $this->faker->name(),
            'ic_no' => $this->faker->unique()->numerify('############'),
            'date_of_birth' => $this->faker->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'email' => $this->faker->email(),
            'phone' => '01'.$this->faker->numerify('########'),
            'agency' => $this->faker->randomElement([
                'JPJ Ipoh',
                'JPJ Taiping',
                'JPJ Seri Manjung',
                'JPJ Teluk Intan'
                'JPJ Gerik'
                'JPJ Stesen Penguatkuasa Gerik'
                'JPJ Stesen Kawalan Sempadan Gerik'
                'JPJ Stesen Penguatkuasa Kuala Kangsar'
                'JPJ UTC Ipoh'
                'JPJ Tapah'

                            ]),
            'rank' => $this->faker->randomElement([
                'Pegawai',
                'Penolong Pegawai',
                'Pembantu Tadbir'
            ]),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => '01'.$this->faker->numerify('########'),
            'is_active' => true,
        ];
    }
}