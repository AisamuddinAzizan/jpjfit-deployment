<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestSessionRequest;
use App\Http\Requests\UpdateTestSessionRequest;
use App\Models\Participant;
use App\Models\TestSession;
use App\Notifications\TestScheduleNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TestSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $allowedStatuses = ['scheduled', 'ongoing', 'completed', 'cancelled'];
        $statusInput = (string) $request->query('status', '');
        $status = in_array($statusInput, $allowedStatuses, true) ? $statusInput : null;

        $sessions = TestSession::query()
            ->withCount('participants')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('session_code', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->latest('session_date')
            ->get();

        return view('test-sessions.index', [
            'sessions' => $sessions,
            'search' => $search,
            'selectedStatus' => $status ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('test-sessions.create', [
            'participants' => Participant::orderBy('full_name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestSessionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $participantIds = $data['participant_ids'] ?? [];
        unset($data['participant_ids']);

        $data['created_by'] = auth()->id();

        $session = TestSession::create($data);
        $session->participants()->sync($participantIds);

        $participants = Participant::whereIn('id', $participantIds)->get();
        foreach ($participants as $participant) {
            if (! empty($participant->email)) {
                $participant->notify(new TestScheduleNotification($session));
            }
        }

        return redirect()->route('test-sessions.index')->with('success', 'Test session created and participants assigned.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TestSession $testSession): View
    {
        $testSession->load(['participants', 'healthRecords', 'fitnessResults']);

        return view('test-sessions.show', compact('testSession'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TestSession $testSession): View
    {
        return view('test-sessions.edit', [
            'testSession' => $testSession->load('participants'),
            'participants' => Participant::orderBy('full_name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestSessionRequest $request, TestSession $testSession): RedirectResponse
    {

        $data = $request->validated();
        $participantIds = $data['participant_ids'] ?? [];
        unset($data['participant_ids']);

        $testSession->update($data);
        $testSession->participants()->sync($participantIds);

        return redirect()->route('test-sessions.index')->with('success', 'Test session updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestSession $testSession): RedirectResponse
    {
        $testSession->delete();

        return redirect()->route('test-sessions.index')->with('success', 'Test session removed.');
    }
}
