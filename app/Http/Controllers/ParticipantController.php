<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Http\Requests\UpdateParticipantRequest;
use App\Models\Participant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $participants = Participant::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('participant_no', 'like', "%{$search}%")
                        ->orWhere('ic_no', 'like', "%{$search}%")
                        ->orWhere('agency', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('participants.index', [
            'participants' => $participants,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('participants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticipantRequest $request): RedirectResponse
    {
        Participant::create($request->validated());

        return redirect()->route('participants.index')->with('success', 'Participant registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Participant $participant): View
    {
        $participant->load([
            'sessions' => fn ($query) => $query->latest('session_date')->take(5),
            'healthRecords' => fn ($query) => $query->latest()->take(5),
            'fitnessResults' => fn ($query) => $query->latest()->take(5),
            'certificates' => fn ($query) => $query->latest()->take(5),
        ]);

        return view('participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Participant $participant): View
    {
        return view('participants.edit', compact('participant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParticipantRequest $request, Participant $participant): RedirectResponse
    {
        $participant->update($request->validated());

        return redirect()->route('participants.index')->with('success', 'Participant profile updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participant $participant): RedirectResponse
    {
        $participant->delete();

        return redirect()->route('participants.index')->with('success', 'Participant deleted successfully.');
    }
}
