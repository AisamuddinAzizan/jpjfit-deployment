@php
    $testSession = $testSession ?? null;
    $editing = isset($testSession);
    $selectedParticipants = old('participant_ids', isset($testSession) ? $testSession->participants->pluck('id')->toArray() : []);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Session Code') }}</label>
        <input class="form-input" name="session_code" type="text" value="{{ old('session_code', $testSession->session_code ?? '') }}" required>
        @error('session_code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Title') }}</label>
        <input class="form-input" name="title" type="text" value="{{ old('title', $testSession->title ?? '') }}" required>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Location') }}</label>
        <input class="form-input" name="location" type="text" value="{{ old('location', $testSession->location ?? '') }}" required>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Date') }}</label>
        <input class="form-input" name="session_date" type="date" value="{{ old('session_date', isset($testSession->session_date) ? $testSession->session_date->format('Y-m-d') : '') }}" required>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Start Time') }}</label>
        <input class="form-input" name="start_time" type="time" value="{{ old('start_time', isset($testSession->start_time) ? substr((string) $testSession->start_time, 0, 5) : '') }}">
        @error('start_time') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('End Time') }}</label>
        <input class="form-input" name="end_time" type="time" value="{{ old('end_time', isset($testSession->end_time) ? substr((string) $testSession->end_time, 0, 5) : '') }}">
        @error('end_time') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Status') }}</label>
        <select class="form-select" name="status" required>
            @foreach(['scheduled' => 'Scheduled', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $testSession->status ?? 'scheduled') === $value)>{{ __($label) }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-slate-700">{{ __('Description') }}</label>
        <textarea class="form-input" name="description" rows="3">{{ old('description', $testSession->description ?? '') }}</textarea>
    </div>
</div>

<div class="mt-5">
    <label class="text-sm font-medium text-slate-700">{{ __('Assign Participants') }}</label>
    <div class="mt-2 grid max-h-56 gap-2 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($participants as $participant)
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500" name="participant_ids[]" value="{{ $participant->id }}" @checked(in_array($participant->id, $selectedParticipants, true))>
                <span>{{ $participant->participant_no }} - {{ $participant->full_name }}</span>
            </label>
        @endforeach
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary" type="submit">{{ $editing ? __('Update Session') : __('Create Session') }}</button>
    <a href="{{ route('test-sessions.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>
