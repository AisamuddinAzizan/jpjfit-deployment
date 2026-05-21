<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFitnessResultRequest;
use App\Http\Requests\UpdateFitnessResultRequest;
use App\Models\FitnessResult;
use App\Models\Participant;
use App\Models\TestSession;
use App\Services\FitnessScoringService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FitnessResultController extends Controller
{
    public function __construct(private readonly FitnessScoringService $scoringService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $results = FitnessResult::query()
            ->with(['participant', 'testSession', 'recorder'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('participant', function ($participantQuery) use ($search) {
                    $participantQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('participant_no', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('fitness-results.index', [
            'results' => $results,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('fitness-results.create', [
            'participants' => Participant::orderBy('full_name')->get(),
            'sessions' => TestSession::orderByDesc('session_date')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFitnessResultRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $scoreData = $this->scoringService->calculate([
            'push_ups' => (int) $data['push_ups'],
            'sit_ups' => (int) $data['sit_ups'],
            'sit_and_reach_cm' => (float) $data['sit_and_reach_cm'],
            'shuttle_run_level' => (float) $data['shuttle_run_level'],
            'run_2_4km_seconds' => (int) $data['run_2_4km_seconds'],
        ]);

        $payload = array_merge($data, $scoreData, ['recorded_by' => auth()->id()]);

        $result = FitnessResult::updateOrCreate(
            [
                'participant_id' => $data['participant_id'],
                'test_session_id' => $data['test_session_id'],
            ],
            $payload,
        );

        $result->testSession->participants()->syncWithoutDetaching([
            $result->participant_id => [
                'result_status' => strtolower($result->result_status),
                'attendance_status' => 'attended',
            ],
        ]);

        return redirect()->route('fitness-results.index')->with('success', 'Fitness result saved with automatic scoring.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FitnessResult $fitnessResult): View
    {
        $fitnessResult->load(['participant', 'testSession', 'recorder']);

        return view('fitness-results.show', compact('fitnessResult'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FitnessResult $fitnessResult): View
    {
        return view('fitness-results.edit', [
            'fitnessResult' => $fitnessResult,
            'participants' => Participant::orderBy('full_name')->get(),
            'sessions' => TestSession::orderByDesc('session_date')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFitnessResultRequest $request, FitnessResult $fitnessResult): RedirectResponse
    {
        $data = $request->validated();

        $scoreData = $this->scoringService->calculate([
            'push_ups' => (int) $data['push_ups'],
            'sit_ups' => (int) $data['sit_ups'],
            'sit_and_reach_cm' => (float) $data['sit_and_reach_cm'],
            'shuttle_run_level' => (float) $data['shuttle_run_level'],
            'run_2_4km_seconds' => (int) $data['run_2_4km_seconds'],
        ]);

        $payload = array_merge($data, $scoreData, ['recorded_by' => auth()->id()]);

        $fitnessResult->update($payload);
        $fitnessResult->testSession->participants()->syncWithoutDetaching([
            $fitnessResult->participant_id => [
                'result_status' => strtolower($fitnessResult->result_status),
                'attendance_status' => 'attended',
            ],
        ]);

        return redirect()->route('fitness-results.index')->with('success', 'Fitness result updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FitnessResult $fitnessResult): RedirectResponse
    {
        $fitnessResult->delete();

        return redirect()->route('fitness-results.index')->with('success', 'Fitness result removed successfully.');
    }
}
