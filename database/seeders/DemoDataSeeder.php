<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\FitnessResult;
use App\Models\HealthRecord;
use App\Models\Participant;
use App\Models\TestSession;
use App\Models\User;
use App\Services\FitnessScoringService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participants = Participant::factory(40)->create();

        $sessionCreator = User::role('jpj_officer')->first() ?? User::first();
        $healthOfficer = User::role('health_officer')->first() ?? $sessionCreator;
        $resultOfficer = User::role('jpj_officer')->first() ?? $sessionCreator;

        $sessions = collect();
        $startYear = now()->year - 4;

        foreach (range($startYear, now()->year) as $year) {
            foreach ([1, 7] as $month) {
                $sessionDate = Carbon::create($year, $month, 15);
                $status = $sessionDate->isFuture() ? 'scheduled' : 'completed';

                $session = TestSession::query()->updateOrCreate(
                    ['session_code' => sprintf('UKJK-%d-%02d', $year, $month)],
                    [
                        'title' => sprintf('UKJK Fitness Assessment %s %d', $sessionDate->format('F'), $year),
                        'location' => $month === 1 ? 'Kompleks Sukan JPJ' : 'Stadium Negeri',
                        'session_date' => $sessionDate->toDateString(),
                        'start_time' => '08:30',
                        'end_time' => '12:30',
                        'status' => $status,
                        'description' => sprintf('Biannual JPJFit session for %d (%s intake).', $year, $month === 1 ? 'January' : 'July'),
                        'created_by' => $sessionCreator?->id,
                    ],
                );

                $sessions->push($session);
            }
        }

        /** @var FitnessScoringService $scoringService */
        $scoringService = app(FitnessScoringService::class);

        foreach ($sessions as $session) {
            $assignedParticipants = $this->pickRandomParticipants($participants, rand(15, 25));

            $syncRows = [];
            foreach ($assignedParticipants as $participant) {
                $syncRows[$participant->id] = [
                    'attendance_status' => 'registered',
                    'result_status' => 'pending',
                ];
            }
            $session->participants()->syncWithoutDetaching($syncRows);

            foreach ($assignedParticipants as $participant) {
                if (rand(1, 100) <= 90) {
                    $height = fake()->randomFloat(2, 150, 190);
                    $weight = fake()->randomFloat(2, 45, 110);
                    $bmi = round($weight / (($height / 100) * ($height / 100)), 2);

                    HealthRecord::updateOrCreate(
                        [
                            'participant_id' => $participant->id,
                            'test_session_id' => $session->id,
                        ],
                        [
                            'recorded_by' => $healthOfficer?->id,
                            'height_cm' => $height,
                            'weight_kg' => $weight,
                            'bmi' => $bmi,
                            'blood_pressure_systolic' => rand(105, 145),
                            'blood_pressure_diastolic' => rand(65, 95),
                            'glucose_mmol' => fake()->randomFloat(2, 3.5, 8.5),
                            'cholesterol_mmol' => fake()->randomFloat(2, 3.0, 8.0),
                            'remarks' => fake()->optional()->sentence(),
                        ],
                    );
                }

                if (rand(1, 100) <= 85) {
                    $metrics = [
                        'push_ups' => rand(5, 70),
                        'sit_ups' => rand(8, 70),
                        'sit_and_reach_cm' => fake()->randomFloat(2, 5, 50),
                        'shuttle_run_level' => fake()->randomFloat(2, 4, 15),
                        'run_2_4km_seconds' => rand(620, 1400),
                    ];

                    $score = $scoringService->calculate($metrics);

                    $result = FitnessResult::updateOrCreate(
                        [
                            'participant_id' => $participant->id,
                            'test_session_id' => $session->id,
                        ],
                        array_merge($metrics, $score, [
                            'recorded_by' => $resultOfficer?->id,
                            'remarks' => fake()->optional()->sentence(),
                        ]),
                    );

                    $session->participants()->syncWithoutDetaching([
                        $participant->id => [
                            'attendance_status' => 'attended',
                            'result_status' => strtolower($result->result_status),
                        ],
                    ]);

                    if ($result->result_status === 'Pass' && rand(1, 100) <= 70) {
                        Certificate::firstOrCreate(
                            [
                                'participant_id' => $participant->id,
                                'test_session_id' => $session->id,
                            ],
                            [
                                'certificate_no' => 'JPJFIT-'.now()->format('Ymd').'-'.str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT),
                                'issued_at' => now()->subDays(rand(0, 30)),
                                'issued_by' => $resultOfficer?->id,
                                'pdf_path' => null,
                            ],
                        );
                    }
                }
            }
        }
    }

    private function pickRandomParticipants(Collection $participants, int $count): Collection
    {
        $count = min($count, $participants->count());

        return $participants->random($count);
    }
}
