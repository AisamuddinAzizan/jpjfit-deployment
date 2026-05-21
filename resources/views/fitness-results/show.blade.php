<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Fitness Result Details') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 text-sm">
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Participant') }}</dt><dd class="font-semibold">{{ $fitnessResult->participant?->full_name }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Session') }}</dt><dd class="font-semibold">{{ $fitnessResult->testSession?->session_code }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Recorded By') }}</dt><dd class="font-semibold">{{ $fitnessResult->recorder?->name ?? '-' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Push-ups') }}</dt><dd class="font-semibold">{{ $fitnessResult->push_ups }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Sit-ups') }}</dt><dd class="font-semibold">{{ $fitnessResult->sit_ups }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Sit & Reach') }}</dt><dd class="font-semibold">{{ $fitnessResult->sit_and_reach_cm }} cm</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Shuttle Run') }}</dt><dd class="font-semibold">{{ $fitnessResult->shuttle_run_level }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('2.4 km') }}</dt><dd class="font-semibold">{{ $fitnessResult->run_2_4km_seconds }} sec</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Total Score') }}</dt><dd class="font-semibold">{{ $fitnessResult->total_score }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Classification') }}</dt><dd class="font-semibold">{{ __($fitnessResult->classification) }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Result') }}</dt><dd class="font-semibold">{{ __($fitnessResult->result_status) }}</dd></div>
        </dl>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('fitness-results.edit', $fitnessResult) }}" class="btn-primary">{{ __('Edit') }}</a>
            <a href="{{ route('fitness-results.index') }}" class="btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="panel-card mt-6 p-5">
        <h2 class="text-lg font-bold text-slate-800">{{ __('Score Mark Range') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('Reference guide for UKJK classification levels.') }}</p>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-2">{{ __('Level') }}</th>
                        <th class="px-3 py-2">{{ __('Score') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-100">
                        <td class="px-3 py-2 font-semibold text-rose-700">{{ __('Poor') }}</td>
                        <td class="px-3 py-2 text-slate-700">0 - 49</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="px-3 py-2 font-semibold text-amber-700">{{ __('Good') }}</td>
                        <td class="px-3 py-2 text-slate-700">70 - 84</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-semibold text-emerald-700">{{ __('Excellent') }}</td>
                        <td class="px-3 py-2 text-slate-700">85 - 100</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
