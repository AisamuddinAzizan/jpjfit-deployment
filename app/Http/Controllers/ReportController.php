<?php

namespace App\Http\Controllers;

use App\Models\FitnessResult;
use App\Models\HealthRecord;
use App\Models\TestSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $sessionId = $request->integer('test_session_id');
        $search = trim((string) $request->query('search', ''));

        // 🟢 DI SINI KOD SUDAH DISUMBAT PENUTUP YANG SANGAT TEPAT
        $baseQuery = FitnessResult::query()
            ->with(['participant', 'testSession'])
            ->whereHas('testSession.participants', function ($query) {
                $query->whereColumn(
                    'participants.id',
                    'fitness_results.participant_id'
                );
            })
            ->when($sessionId, fn ($query, $id) => $query->where('test_session_id', $id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('participant', function ($participantQuery) use ($search) {
                        $participantQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('participant_no', 'like', "%{$search}%");
                    })->orWhereHas('testSession', function ($sessionQuery) use ($search) {
                        $sessionQuery->where('session_code', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%");
                    });
                });
            });

        $results = (clone $baseQuery)
            ->latest()
            ->get();

        $statsQuery = clone $baseQuery;
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pass' => (clone $statsQuery)->where('result_status', 'Pass')->count(),
            'fail' => (clone $statsQuery)->where('result_status', 'Fail')->count(),
            'avg_score' => round((float) ((clone $statsQuery)->avg('total_score') ?? 0), 2),
        ];

        $participantIds = $results
            ->pluck('participant_id')
            ->filter()
            ->unique()
            ->values();

        $healthRecords = HealthRecord::query()
            ->with(['participant:id,participant_no,full_name', 'testSession:id,session_date'])
            ->whereIn('participant_id', $participantIds)
            ->whereNotNull('cholesterol_mmol')
            ->orderBy('participant_id')
            ->get();

        $cholesterolHistoryByParticipant = $healthRecords
            ->groupBy('participant_id')
            ->map(function (Collection $records): array {
                $records = $records->sortBy(function ($record) {
                    return $record->testSession?->session_date ?? $record->created_at;
                });

                return $records->map(function (HealthRecord $record): array {
                    $meta = $this->cholesterolMeta((float) $record->cholesterol_mmol);

                    return [
                        'date' => $record->testSession?->session_date?->format('d M Y') ?? $record->created_at?->format('d M Y') ?? '-',
                        'value' => round((float) $record->cholesterol_mmol, 2),
                        'level' => $meta['level'],
                        'color' => $meta['color'],
                    ];
                })->values()->all();
            });

        $cholesterolParticipants = $healthRecords
            ->groupBy('participant_id')
            ->map(function (Collection $records, int|string $participantId): array {
                /** @var HealthRecord $latest */
                $latest = $records->last();
                $meta = $this->cholesterolMeta((float) $latest->cholesterol_mmol);

                return [
                    'participant_id' => (int) $participantId,
                    'participant_name' => $latest->participant?->full_name ?? '-',
                    'participant_no' => $latest->participant?->participant_no ?? '-',
                    'current_value' => round((float) $latest->cholesterol_mmol, 2),
                    'recorded_at' => $latest->testSession?->session_date?->format('d M Y') ?? $latest->created_at?->format('d M Y') ?? '-',
                    'level' => $meta['level'],
                    'color' => $meta['color'],
                ];
            })
            ->sortBy('participant_name')
            ->values();

        $defaultTrendParticipantId = (int) ($cholesterolParticipants->first()['participant_id'] ?? 0);

        return view('reports.index', [
            'results' => $results,
            'stats' => $stats,
            'sessions' => TestSession::orderByDesc('session_date')->get(),
            'selectedSession' => $sessionId,
            'search' => $search,
            'cholesterolParticipants' => $cholesterolParticipants,
            'cholesterolHistoryByParticipant' => $cholesterolHistoryByParticipant,
            'defaultTrendParticipantId' => $defaultTrendParticipantId,
        ]);
    }

    /**
     * @return array{level: string, color: string}
     */
    private function cholesterolMeta(float $value): array
    {
        if ($value <= 5.2) {
            return ['level' => 'Excellent', 'color' => '#16a34a'];
        }

        if ($value <= 6.2) {
            return ['level' => 'Good', 'color' => '#2563eb'];
        }

        if ($value <= 7.0) {
            return ['level' => 'Moderate', 'color' => '#eab308'];
        }

        return ['level' => 'Poor', 'color' => '#dc2626'];
    }

    public function exportPdf(Request $request)
    {
        $sessionId = $request->integer('test_session_id');
        $search = trim((string) $request->query('search', ''));

        // 1. Ambil data Fitness Result dengan kueri yang bersih
        $results = FitnessResult::query()
            ->with(['participant', 'testSession'])
            ->when($sessionId, function ($query) use ($sessionId) {
                return $query->where('test_session_id', $sessionId);
            })
            ->when($search !== '', function ($query) use ($search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('participant', function ($participantQuery) use ($search) {
                        $participantQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('participant_no', 'like', "%{$search}%");
                    })->orWhereHas('testSession', function ($sessionQuery) use ($search) {
                        $sessionQuery->where('session_code', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->get();

        // 2. Ekstrak ID Peserta & ID Sesi
        $participantIds = $results->pluck('participant_id')->filter()->unique()->values()->all();
        $sessionIds = $results->pluck('test_session_id')->filter()->unique()->values()->all();

        // 3. Tarik data Health Record yang sepadan
        $healthRecords = HealthRecord::query()
            ->whereIn('participant_id', $participantIds)
            ->whereIn('test_session_id', $sessionIds)
            ->get()
            ->keyBy(function (HealthRecord $record) {
                return $record->participant_id . '-' . $record->test_session_id;
            });

        // 4. Hantar data ke template Blade
        $pdf = Pdf::loadView('reports.fitness-pdf', [
            'results' => $results,
            'healthRecords' => $healthRecords,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('fitness-health-report-'.now()->format('YmdHis').'.pdf');
    }
}