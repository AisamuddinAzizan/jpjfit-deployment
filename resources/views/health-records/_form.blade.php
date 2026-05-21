@php
    $healthRecord = $healthRecord ?? null;
    $editing = isset($healthRecord);
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

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Participant') }}</label>
        <select class="form-select" name="participant_id" required>
            @foreach($participants as $participant)
                <option value="{{ $participant->id }}" @selected(old('participant_id', $healthRecord->participant_id ?? '') == $participant->id)>
                    {{ $participant->participant_no }} - {{ $participant->full_name }}
                </option>
            @endforeach
        </select>
        @error('participant_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Test Session') }}</label>
        <select class="form-select" name="test_session_id" required>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}" @selected(old('test_session_id', $healthRecord->test_session_id ?? '') == $session->id)>
                    {{ $session->session_code }} - {{ $session->title }}
                </option>
            @endforeach
        </select>
        @error('test_session_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Height (cm)') }}</label>
        <input class="form-input" name="height_cm" type="number" step="0.01" value="{{ old('height_cm', $healthRecord->height_cm ?? '') }}" required>
        @error('height_cm') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Weight (kg)') }}</label>
        <input class="form-input" name="weight_kg" type="number" step="0.01" value="{{ old('weight_kg', $healthRecord->weight_kg ?? '') }}" required>
        @error('weight_kg') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Blood Pressure Systolic') }}</label>
        <input class="form-input" name="blood_pressure_systolic" type="number" value="{{ old('blood_pressure_systolic', $healthRecord->blood_pressure_systolic ?? '') }}" required>
        @error('blood_pressure_systolic') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Blood Pressure Diastolic') }}</label>
        <input class="form-input" name="blood_pressure_diastolic" type="number" value="{{ old('blood_pressure_diastolic', $healthRecord->blood_pressure_diastolic ?? '') }}" required>
        @error('blood_pressure_diastolic') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Glucose (mmol/L)') }}</label>
        <input class="form-input" name="glucose_mmol" type="number" step="0.01" value="{{ old('glucose_mmol', $healthRecord->glucose_mmol ?? '') }}">
        @error('glucose_mmol') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">{{ __('Cholesterol (mmol/L)') }}</label>
        <input class="form-input" name="cholesterol_mmol" type="number" step="0.01" value="{{ old('cholesterol_mmol', $healthRecord->cholesterol_mmol ?? '') }}">
        @error('cholesterol_mmol') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-slate-700">{{ __('Remarks') }}</label>
        <textarea class="form-input" name="remarks" rows="3">{{ old('remarks', $healthRecord->remarks ?? '') }}</textarea>
        @error('remarks') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button class="btn-primary" type="submit">{{ $editing ? __('Update Record') : __('Save Record') }}</button>
    <a href="{{ route('health-records.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>
<div class="mt-10 rounded-xl border border-slate-200 bg-slate-50 p-5">
    <h3 class="mb-4 text-xl font-bold text-slate-800">
        Health Reference Guide
    </h3>

    {{-- BMI Reference --}}
    <div class="mb-6">
        <h4 class="mb-3 text-lg font-bold text-slate-700">BMI Classification</h4>

        <table class="min-w-full border border-slate-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="border px-4 py-2 text-left">BMI</th>
                    <th class="border px-4 py-2 text-left">Classification</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td class="border px-4 py-2">Below 18.5</td>
                    <td class="border px-4 py-2 text-blue-700 font-semibold">Underweight</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">18.5 - 24.9</td>
                    <td class="border px-4 py-2 text-emerald-700 font-semibold">Normal</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">25.0 - 29.9</td>
                    <td class="border px-4 py-2 text-amber-700 font-semibold">Overweight</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">30 and above</td>
                    <td class="border px-4 py-2 text-rose-700 font-semibold">Obese</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Blood Pressure --}}
    <div class="mb-6">
        <h4 class="mb-3 text-lg font-bold text-slate-700">Blood Pressure Reference</h4>

        <table class="min-w-full border border-slate-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Category</th>
                    <th class="border px-4 py-2 text-left">Reading</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td class="border px-4 py-2 text-emerald-700 font-semibold">Normal</td>
                    <td class="border px-4 py-2">Below 120 / 80</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2 text-amber-700 font-semibold">Elevated</td>
                    <td class="border px-4 py-2">120-129 / Below 80</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2 text-rose-700 font-semibold">High</td>
                    <td class="border px-4 py-2">130+ / 80+</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Glucose --}}
    <div class="mb-6">
        <h4 class="mb-3 text-lg font-bold text-slate-700">Glucose Reference</h4>

        <table class="min-w-full border border-slate-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Reading</th>
                    <th class="border px-4 py-2 text-left">Status</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td class="border px-4 py-2">Below 5.6 mmol/L</td>
                    <td class="border px-4 py-2 text-emerald-700 font-semibold">Normal</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">5.6 - 6.9 mmol/L</td>
                    <td class="border px-4 py-2 text-amber-700 font-semibold">Prediabetes</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">7.0 mmol/L and above</td>
                    <td class="border px-4 py-2 text-rose-700 font-semibold">Diabetes Risk</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Cholesterol --}}
    <div>
        <h4 class="mb-3 text-lg font-bold text-slate-700">Cholesterol Reference</h4>

        <table class="min-w-full border border-slate-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Reading</th>
                    <th class="border px-4 py-2 text-left">Status</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                <tr>
                    <td class="border px-4 py-2">Below 5.2 mmol/L</td>
                    <td class="border px-4 py-2 text-emerald-700 font-semibold">Normal</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">5.2 - 6.2 mmol/L</td>
                    <td class="border px-4 py-2 text-amber-700 font-semibold">Borderline</td>
                </tr>

                <tr>
                    <td class="border px-4 py-2">Above 6.2 mmol/L</td>
                    <td class="border px-4 py-2 text-rose-700 font-semibold">High</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>