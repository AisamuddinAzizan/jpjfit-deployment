<?php

namespace App\Services;

class FitnessScoringService
{
    /**
     * Calculate aggregate score and classification based on UKJK metrics.
     *
     * @param array<string, int|float> $metrics
     * @return array{total_score: float, classification: string, result_status: string}
     */
    public function calculate(array $metrics): array
    {
        $pushUpsScore = min(20, ($metrics['push_ups'] / 60) * 20);
        $sitUpsScore = min(20, ($metrics['sit_ups'] / 60) * 20);
        $sitReachScore = min(20, ($metrics['sit_and_reach_cm'] / 45) * 20);
        $shuttleScore = min(20, ($metrics['shuttle_run_level'] / 15) * 20);

        // Faster run time gets higher score; clamp to 0..20.
        $runSeconds = max(1, (int) $metrics['run_2_4km_seconds']);
        $runScore = max(0, min(20, ((1200 - $runSeconds) / 480) * 20));

        $total = round($pushUpsScore + $sitUpsScore + $sitReachScore + $shuttleScore + $runScore, 2);

        if ($total >= 85) {
            $classification = 'Excellent';
        } elseif ($total >= 70) {
            $classification = 'Good';
        } elseif ($total >= 50) {
            $classification = 'Average';
        } else {
            $classification = 'Poor';
        }

        return [
            'total_score' => $total,
            'classification' => $classification,
            'result_status' => $total >= 50 ? 'Pass' : 'Fail',
        ];
    }
}
