@php
    $fitnessResult = $fitnessResult ?? null;
    $editing = isset($fitnessResult);
@endphp

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <p class="font-semibold">{{ __('Please fix the following errors:') }}</p>
        <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <label class="text-sm font-medium text-slate-700">{{ __('Participant') }}</label>
        <select class="form-select" name="participant_id" required>
            <option value="">{{ __('Choose participant') }}</option>
            @foreach($participants as $participant)
                <option value="{{ $participant->id }}" @selected(old('participant_id', $fitnessResult->participant_id ?? '') == $participant->id)>
                    {{ $participant->participant_no }} - {{ $participant->full_name }}
                </option>
            @endforeach
        </select>
        @error('participant_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Test Session') }}</label>
        <select class="form-select" name="test_session_id" required>
            <option value="">{{ __('Choose session') }}</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}" @selected(old('test_session_id', $fitnessResult->test_session_id ?? '') == $session->id)>
                    {{ $session->session_code }}
                </option>
            @endforeach
        </select>
        @error('test_session_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Push-ups') }}</label>
        <input class="form-input" name="push_ups" type="number" value="{{ old('push_ups', $fitnessResult->push_ups ?? '') }}" required>
        @error('push_ups') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Sit-ups') }}</label>
        <input class="form-input" name="sit_ups" type="number" value="{{ old('sit_ups', $fitnessResult->sit_ups ?? '') }}" required>
        @error('sit_ups') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Sit & Reach (cm)') }}</label>
        <input class="form-input" name="sit_and_reach_cm" type="number" step="0.01" value="{{ old('sit_and_reach_cm', $fitnessResult->sit_and_reach_cm ?? '') }}" required>
        @error('sit_and_reach_cm') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Shuttle Run Level') }}</label>
        <input class="form-input" name="shuttle_run_level" type="number" step="0.01" value="{{ old('shuttle_run_level', $fitnessResult->shuttle_run_level ?? '') }}" required>
        @error('shuttle_run_level') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('2.4 km Time (seconds)') }}</label>
        <input class="form-input" name="run_2_4km_seconds" type="number" value="{{ old('run_2_4km_seconds', $fitnessResult->run_2_4km_seconds ?? '') }}" required>
        @error('run_2_4km_seconds') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 lg:col-span-3">
        <label class="text-sm font-medium text-slate-700">{{ __('Remarks') }}</label>
        <textarea class="form-input" name="remarks" rows="3">{{ old('remarks', $fitnessResult->remarks ?? '') }}</textarea>
        @error('remarks') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 rounded-xl border border-teal-200 bg-teal-50 p-3 text-sm text-teal-700">
    {{ __('Total score, classification, and pass/fail status are calculated automatically during submission.') }}
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary" type="submit">{{ $editing ? __('Update Result') : __('Save Result') }}</button>
    <a href="{{ route('fitness-results.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>
<div class="mt-10 rounded-xl border border-slate-200 bg-slate-50 p-5">
    <h3 class="mb-4 text-xl font-bold text-slate-800">
        Fitness Test Grading Reference
    </h3>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-slate-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Test</th>
                    <th class="border px-4 py-2 text-left">Formula</th>
                    <th class="border px-4 py-2 text-left">Maximum Score</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td class="border px-4 py-2">Push-ups</td>
                    <td class="border px-4 py-2">(Push-ups ÷ 60) × 20</td>
                    <td class="border px-4 py-2">20</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">Sit-ups</td>
                    <td class="border px-4 py-2">(Sit-ups ÷ 60) × 20</td>
                    <td class="border px-4 py-2">20</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">Sit & Reach</td>
                    <td class="border px-4 py-2">(cm ÷ 45) × 20</td>
                    <td class="border px-4 py-2">20</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">Shuttle Run</td>
                    <td class="border px-4 py-2">(Level ÷ 15) × 20</td>
                    <td class="border px-4 py-2">20</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">2.4 km Run</td>
                    <td class="border px-4 py-2">((1200 - seconds) ÷ 480) × 20</td>
                    <td class="border px-4 py-2">20</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <h4 class="mb-3 text-lg font-bold text-slate-700">
            Classification Reference
        </h4>

        <table class="min-w-full border border-slate-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Score Range</th>
                    <th class="border px-4 py-2 text-left">Grade</th>
                    <th class="border px-4 py-2 text-left">Status</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td class="border px-4 py-2">85 - 100</td>
                    <td class="border px-4 py-2 font-semibold text-emerald-700">Excellent</td>
                    <td class="border px-4 py-2">Pass</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">70 - 84</td>
                    <td class="border px-4 py-2 font-semibold text-sky-700">Good</td>
                    <td class="border px-4 py-2">Pass</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">50 - 69</td>
                    <td class="border px-4 py-2 font-semibold text-amber-700">Average</td>
                    <td class="border px-4 py-2">Pass</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">0 - 49</td>
                    <td class="border px-4 py-2 font-semibold text-rose-700">Poor</td>
                    <td class="border px-4 py-2">Fail</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-10 rounded-xl border border-slate-200 bg-slate-50 p-5">


