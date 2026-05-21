<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthRecordRequest;
use App\Http\Requests\UpdateHealthRecordRequest;
use App\Models\HealthRecord;
use App\Models\Participant;
use App\Models\TestSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $records = HealthRecord::query()
            ->with(['participant', 'testSession', 'recorder'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('participant', function ($participantQuery) use ($search) {
                    $participantQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('participant_no', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('health-records.index', [
            'records' => $records,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('health-records.create', [
            'participants' => Participant::orderBy('full_name')->get(),
            'sessions' => TestSession::orderByDesc('session_date')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHealthRecordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $heightMeters = $data['height_cm'] / 100;
        $data['bmi'] = round($data['weight_kg'] / ($heightMeters * $heightMeters), 2);
        $data['recorded_by'] = auth()->id();

        $bmi = $data['bmi'];

if ($bmi < 18.5) {
    $data['bmi_status'] = 'Underweight';
} elseif ($bmi < 25) {
    $data['bmi_status'] = 'Normal';
} elseif ($bmi < 30) {
    $data['bmi_status'] = 'Overweight';
} else {
    $data['bmi_status'] = 'Obese';
}

        $cholesterol = $data['cholesterol_mmol'] ?? 0;

if ($cholesterol < 5.2) {
    $data['cholesterol_status'] = 'Normal';
} elseif ($cholesterol <= 6.2) {
    $data['cholesterol_status'] = 'Borderline';
} else {
    $data['cholesterol_status'] = 'High';
}
        HealthRecord::updateOrCreate(
            [
                'participant_id' => $data['participant_id'],
                'test_session_id' => $data['test_session_id'],
            ],
            $data,
        );

        return redirect()->route('health-records.index')->with('success', 'Health record saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthRecord $healthRecord): View
    {
        $healthRecord->load(['participant', 'testSession', 'recorder']);

        return view('health-records.show', compact('healthRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthRecord $healthRecord): View
    {
        return view('health-records.edit', [
            'healthRecord' => $healthRecord,
            'participants' => Participant::orderBy('full_name')->get(),
            'sessions' => TestSession::orderByDesc('session_date')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthRecordRequest $request, HealthRecord $healthRecord): RedirectResponse
    {
        $data = $request->validated();
        $heightMeters = $data['height_cm'] / 100;
        $data['bmi'] = round($data['weight_kg'] / ($heightMeters * $heightMeters), 2);
        $data['recorded_by'] = auth()->id();

        $bmi = $data['bmi'];

if ($bmi < 18.5) {
    $data['bmi_status'] = 'Underweight';
} elseif ($bmi < 25) {
    $data['bmi_status'] = 'Normal';
} elseif ($bmi < 30) {
    $data['bmi_status'] = 'Overweight';
} else {
    $data['bmi_status'] = 'Obese';
}

        $cholesterol = $data['cholesterol_mmol'] ?? 0;

    if ($cholesterol < 5.2) {
    $data['cholesterol_status'] = 'Normal';
    } elseif ($cholesterol <= 6.2) {
    $data['cholesterol_status'] = 'Borderline';
    } else {
    $data['cholesterol_status'] = 'High';
    }

        $healthRecord->update($data);

        return redirect()->route('health-records.index')->with('success', 'Health record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthRecord $healthRecord): RedirectResponse
    {
        $healthRecord->delete();

        return redirect()->route('health-records.index')->with('success', 'Health record removed successfully.');
    }
}
