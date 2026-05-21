<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Session Details') }}</h1>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="panel-card p-5 lg:col-span-1">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Session Info') }}</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Code') }}</dt><dd class="font-semibold">{{ $testSession->session_code }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Title') }}</dt><dd class="font-semibold">{{ $testSession->title }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Date') }}</dt><dd class="font-semibold">{{ $testSession->session_date?->format('d M Y') }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Location') }}</dt><dd class="font-semibold">{{ $testSession->location }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">{{ __('Status') }}</dt><dd class="font-semibold">{{ __(ucfirst($testSession->status)) }}</dd></div>
            </dl>
        </div>
        <div class="panel-card p-5 lg:col-span-2">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Assigned Participants') }}</h2>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @forelse($testSession->participants as $participant)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">{{ $participant->participant_no }} - {{ $participant->full_name }}</div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No participants assigned yet.') }}</p>
                @endforelse
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('test-sessions.edit', $testSession) }}" class="btn-primary">{{ __('Edit Session') }}</a>
                <a href="{{ route('test-sessions.index') }}" class="btn-secondary">{{ __('Back') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
