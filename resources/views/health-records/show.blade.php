<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Health Record Details') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Participant') }}</dt><dd class="font-semibold">{{ $healthRecord->participant?->full_name }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Session') }}</dt><dd class="font-semibold">{{ $healthRecord->testSession?->session_code }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Recorded By') }}</dt><dd class="font-semibold">{{ $healthRecord->recorder?->name ?? '-' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Height') }}</dt><dd class="font-semibold">{{ $healthRecord->height_cm }} cm</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Weight') }}</dt><dd class="font-semibold">{{ $healthRecord->weight_kg }} kg</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">BMI</dt><dd class="font-semibold">{{ $healthRecord->bmi }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Blood Pressure') }}</dt><dd class="font-semibold">{{ $healthRecord->blood_pressure_systolic }}/{{ $healthRecord->blood_pressure_diastolic }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Glucose') }}</dt><dd class="font-semibold">{{ $healthRecord->glucose_mmol ?? '-' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Cholesterol') }}</dt><dd class="font-semibold">{{ $healthRecord->cholesterol_mmol ?? '-' }}</dd></div>
        </dl>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('health-records.edit', $healthRecord) }}" class="btn-primary">{{ __('Edit') }}</a>
            <a href="{{ route('health-records.index') }}" class="btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>
</x-app-layout>
