<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Participant Profile') }}</h1>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="panel-card p-5 lg:col-span-1">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Personal Details') }}</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Participant No') }}</dt><dd class="font-semibold">{{ $participant->participant_no }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Full Name') }}</dt><dd class="font-semibold">{{ $participant->full_name }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('IC No') }}</dt><dd class="font-semibold">{{ $participant->ic_no }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Gender') }}</dt><dd class="font-semibold">{{ $participant->gender === 'male' ? __('Male') : __('Female') }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Phone') }}</dt><dd class="font-semibold">{{ $participant->phone ?? '-' }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Agency') }}</dt><dd class="font-semibold">{{ $participant->agency ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="panel-card p-5 lg:col-span-2">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Recent Sessions') }}</h2>
            <div class="mt-3 space-y-2">
                @forelse($participant->sessions as $session)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                        {{ $session->title }} ({{ $session->session_code }}) - {{ $session->session_date?->format('d M Y') }}
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No session record yet.') }}</p>
                @endforelse
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('participants.edit', $participant) }}" class="btn-primary">{{ __('Edit') }}</a>
                <a href="{{ route('participants.index') }}" class="btn-secondary">{{ __('Back') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
