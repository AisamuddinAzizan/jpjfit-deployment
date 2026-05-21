<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Dashboard Overview') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Monitoring participant readiness and UKJK outcomes in real time.') }}</p>
            </div>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="btn-secondary" type="submit">{{ __('Mark Notifications as Read') }}</button>
            </form>
        </div>
    </x-slot>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="metric-card">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ __('Participants') }}</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900" data-counter-target="{{ $stats['participants'] }}">0</p>
        </article>
        <article class="metric-card">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ __('Upcoming Tests') }}</p>
            <p class="mt-3 text-3xl font-extrabold text-slate-900" data-counter-target="{{ $stats['upcoming_tests'] }}">0</p>
        </article>
        <article class="metric-card">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ __('Pass Results') }}</p>
            <p class="mt-3 text-3xl font-extrabold text-emerald-700" data-counter-target="{{ $stats['pass_count'] }}">0</p>
        </article>
        <article class="metric-card">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ __('Fail Results') }}</p>
            <p class="mt-3 text-3xl font-extrabold text-rose-700" data-counter-target="{{ $stats['fail_count'] }}">0</p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="panel-card p-5 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">{{ __('Session Pass/Fail by Date') }}</h2>
                <span class="text-xs text-slate-500">{{ __('Last 10 years') }}</span>
            </div>
            <div class="h-72">
                <canvas id="sessionsChart"
                    data-labels='@json($sessionOutcomeSeries->pluck("label")->values())'
                    data-pass-values='@json($sessionOutcomeSeries->pluck("pass")->values())'
                    data-fail-values='@json($sessionOutcomeSeries->pluck("fail")->values())'
                    data-pass-label="{{ __('Pass') }}"
                    data-fail-label="{{ __('Fail') }}"></canvas>
            </div>
        </div>

        <div class="panel-card p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">{{ __('Classification Mix') }}</h2>
            </div>
            <div class="h-72">
                <canvas id="classificationChart"
                    data-labels='@json(collect(array_keys($classificationBreakdown->toArray()))->map(fn ($label) => __($label))->values())'
                    data-values='@json(array_values($classificationBreakdown->toArray()))'></canvas>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="panel-card p-5">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Upcoming / Recent Sessions') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse($recentSessions as $session)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-slate-800">{{ $session->title }}</p>
                            <span class="rounded-full bg-teal-100 px-2 py-1 text-xs font-semibold text-teal-700">{{ strtoupper($session->status) }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ $session->session_code }} | {{ $session->session_date?->format('d M Y') }} | {{ $session->location }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Participants') }}: {{ $session->participants_count }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No sessions available yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="panel-card p-5">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Notifications') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse($notifications as $notification)
                    <div class="rounded-xl border p-3 {{ $notification->read_at ? 'border-slate-200 bg-slate-50' : 'border-teal-200 bg-teal-50' }}">
                        <p class="font-semibold text-slate-800">{{ $notification->data['title'] ?? __('System Notification') }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $notification->data['message'] ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('No notifications yet.') }}</p>
                @endforelse
            </div>
        </div>
    </section>

    @if(auth()->user()->hasRole('admin'))
        <section class="mt-6">
            <div class="panel-card p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-slate-800">{{ __('Newsletter Subscribers') }}</h2>
                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                        {{ __('Total') }}: {{ $newsletterSubscribers->count() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Subscribed At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($newsletterSubscribers as $subscriber)
                                <tr>
                                    <td>{{ $subscriber->name ?: '-' }}</td>
                                    <td>{{ $subscriber->email }}</td>
                                    <td>{{ $subscriber->subscribed_at?->format('d M Y H:i') ?? $subscriber->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-slate-500">{{ __('No subscribers yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
</x-app-layout>
