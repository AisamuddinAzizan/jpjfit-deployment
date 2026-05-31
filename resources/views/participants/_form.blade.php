@php
    $participant = $participant ?? null;
    $editing = isset($participant);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Full Name') }}</label>
        <input class="form-input" name="full_name" type="text" value="{{ old('full_name', $participant->full_name ?? '') }}" required>
        @error('full_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('IC No') }}</label>
        <input class="form-input" name="ic_no" type="text" value="{{ old('ic_no', $participant->ic_no ?? '') }}" required>
        @error('ic_no') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Date of Birth') }}</label>
        <input class="form-input" name="date_of_birth" type="date" value="{{ old('date_of_birth', isset($participant->date_of_birth) ? $participant->date_of_birth->format('Y-m-d') : '') }}">
        @error('date_of_birth') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Gender') }}</label>
        <select class="form-select" name="gender" required>
            <option value="male" @selected(old('gender', $participant->gender ?? '') === 'male')>{{ __('Male') }}</option>
            <option value="female" @selected(old('gender', $participant->gender ?? '') === 'female')>{{ __('Female') }}</option>
        </select>
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Email') }}</label>
        <input class="form-input" name="email" type="email" value="{{ old('email', $participant->email ?? '') }}">
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Phone') }}</label>
        <input class="form-input" name="phone" type="text" value="{{ old('phone', $participant->phone ?? '') }}">
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Agency') }}</label>
        <input class="form-input" name="agency" type="text" value="{{ old('agency', $participant->agency ?? '') }}">
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Rank') }}</label>
        <input class="form-input" name="rank" type="text" value="{{ old('rank', $participant->rank ?? '') }}">
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Emergency Contact Name') }}</label>
        <input class="form-input" name="emergency_contact_name" type="text" value="{{ old('emergency_contact_name', $participant->emergency_contact_name ?? '') }}">
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Emergency Contact Phone') }}</label>
        <input class="form-input" name="emergency_contact_phone" type="text" value="{{ old('emergency_contact_phone', $participant->emergency_contact_phone ?? '') }}">
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-slate-700">{{ __('Address') }}</label>
        <textarea class="form-input" name="address" rows="3">{{ old('address', $participant->address ?? '') }}</textarea>
    </div>
    <div>
        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500" @checked(old('is_active', $participant->is_active ?? true))>
            {{ __('Active') }}
        </label>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-primary">{{ $editing ? __('Update Participant') : __('Save Participant') }}</button>
    <a href="{{ route('participants.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>
