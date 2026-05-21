<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Health Records (KKM)') }}</h1>
            <a href="{{ route('health-records.create') }}" class="btn-primary">{{ __('Add Record') }}</a>
        </div>
    </x-slot>

    <div class="panel-card p-5">
        <form method="GET" action="{{ route('health-records.index') }}" class="grid gap-3 md:grid-cols-3">
            <input type="text" name="search" class="form-input md:col-span-2" value="{{ $search }}" placeholder="{{ __('Search participant name or number') }}">
            <button class="filter-btn" type="submit">{{ __('Search') }}</button>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Participant') }}</th>
                        <th>{{ __('Session') }}</th>
                        <th>BMI</th>
                        <th>{{ __('Blood Pressure') }}</th>
                        <th>{{ __('Glucose') }}</th>
                        <th>{{ __('Cholesterol Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->participant?->full_name }}</td>
                            <td>{{ $record->testSession?->session_code }}</td>
                            <td>
    <div class="flex flex-col gap-1">
        <span>{{ $record->bmi }}</span>

        @if($record->bmi_status == 'Underweight')
            <span style="background:#dbeafe;color:#1d4ed8;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;width:fit-content;">
                Underweight
            </span>

        @elseif($record->bmi_status == 'Normal')
            <span style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;width:fit-content;">
                Normal
            </span>

        @elseif($record->bmi_status == 'Overweight')
            <span style="background:#fed7aa;color:#c2410c;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;width:fit-content;display:inline-block;">
                Overweight
            </span>

        @elseif($record->bmi_status == 'Obese')
            <span style="background:#fee2e2;color:#b91c1c;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;width:fit-content;">
                Obese
            </span>

        @else
            <span>-</span>
        @endif
    </div>
</td>

                            <td>{{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }}</td>
                            <td>{{ $record->glucose_mmol ?? '-' }}</td>
                      
                            <td>
    @php
        $status = strtolower(trim($record->cholesterol_status));
    @endphp

    @if($status == 'normal')
        <span style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;">
            Normal
        </span>

    @elseif($status == 'borderline')
        <span style="background:#fef3c7;color:#b45309;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;">
            Borderline
        </span>

    @elseif($status == 'high')
        <span style="background:#fee2e2;color:#b91c1c;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;">
            High
        </span>

    @else
        <span style="background:#e5e7eb;color:#374151;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:bold;">
            {{ $record->cholesterol_status }}
        </span>
    @endif
</td>
                            <td class="text-right">
                                <a href="{{ route('health-records.show', $record) }}" class="text-sm font-semibold text-sky-700">{{ __('View') }}</a>
                                <a href="{{ route('health-records.edit', $record) }}" class="ml-3 text-sm font-semibold text-teal-700">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('health-records.destroy', $record) }}" class="ml-3 inline" onsubmit="return confirm('{{ __('Delete record?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-rose-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-500">{{ __('No health records found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
