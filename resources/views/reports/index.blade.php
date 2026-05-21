<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Fitness Reports') }}</h1>
    </x-slot>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article class="metric-card"><p class="text-xs uppercase tracking-wider text-slate-500">{{ __('Total Results') }}</p><p class="mt-3 text-3xl font-extrabold">{{ $stats['total'] }}</p></article>
        <article class="metric-card"><p class="text-xs uppercase tracking-wider text-slate-500">{{ __('Pass') }}</p><p class="mt-3 text-3xl font-extrabold text-emerald-700">{{ $stats['pass'] }}</p></article>
        <article class="metric-card"><p class="text-xs uppercase tracking-wider text-slate-500">{{ __('Fail') }}</p><p class="mt-3 text-3xl font-extrabold text-rose-700">{{ $stats['fail'] }}</p></article>
        <article class="metric-card"><p class="text-xs uppercase tracking-wider text-slate-500">{{ __('Average Score') }}</p><p class="mt-3 text-3xl font-extrabold text-sky-700">{{ $stats['avg_score'] }}</p></article>
    </section>

    <div class="panel-card mt-6 p-5">
        <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 md:grid-cols-5">
            <input type="text" name="search" class="form-input md:col-span-2" value="{{ $search }}" placeholder="{{ __('Search participant or session') }}">
            <select name="test_session_id" class="form-select md:col-span-2">
                <option value="">{{ __('All Sessions') }}</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}" @selected((int)$selectedSession === $session->id)>
                        {{ $session->session_code }} - {{ $session->title }}
                    </option>
                @endforeach
            </select>
            <button class="filter-btn" type="submit">{{ __('Apply Filter') }}</button>
            <div class="flex gap-2">
                <a class="btn-primary" href="{{ route('reports.export.csv', request()->query()) }}">{{ __('Export CSV') }}</a>
                <a class="btn-secondary" href="{{ route('reports.export.pdf', request()->query()) }}">{{ __('Export PDF') }}</a>
            </div>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Participant') }}</th>
                        <th>{{ __('Session') }}</th>
                        <th>{{ __('Score') }}</th>
                        <th>{{ __('Classification') }}</th>
                        <th>{{ __('Result') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $result->participant?->full_name }}</td>
                            <td>{{ $result->testSession?->session_code }}</td>
                            <td>{{ $result->total_score }}</td>
                            <td>{{ __($result->classification) }}</td>
                            <td>{{ __($result->result_status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-slate-500">{{ __('No report data available.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card mt-6 p-5" id="cholesterolTrendSection"
        data-history='@json($cholesterolHistoryByParticipant)'
        data-default-participant-id="{{ $defaultTrendParticipantId }}"
        data-y-label="{{ __('Cholesterol (mmol/L)') }}"
        data-level-excellent="{{ __('Excellent') }}"
        data-level-good="{{ __('Good') }}"
        data-level-moderate="{{ __('Moderate') }}"
        data-level-poor="{{ __('Poor') }}"
        data-no-data="{{ __('No cholesterol trend data available for this participant.') }}">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">{{ __('Cholesterol Monitoring') }}</h2>
                <p class="text-sm text-slate-500">{{ __('Periodic cholesterol indicator and trend by participant.') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                <span class="mr-3 inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>{{ __('Poor') }}</span>
                <span class="mr-3 inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span>{{ __('Moderate') }}</span>
                <span class="mr-3 inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>{{ __('Good') }}</span>
                <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-green-600"></span>{{ __('Excellent') }}</span>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Participant') }}</th>
                        <th>{{ __('Participant No') }}</th>
                        <th>{{ __('Current Cholesterol') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cholesterolParticipants as $item)
                        <tr>
                            <td>{{ $item['participant_name'] }}</td>
                            <td>{{ $item['participant_no'] }}</td>
                            <td>{{ number_format((float) $item['current_value'], 2) }} mmol/L</td>
                            <td>
                                <span class="inline-flex items-center gap-2 rounded-full px-2 py-1 text-xs font-semibold"
                                    style="background-color: {{ $item['color'] }}22; color: {{ $item['color'] }};">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $item['color'] }};"></span>
                                    {{ __($item['level']) }}
                                </span>
                            </td>
                            <td>{{ $item['recorded_at'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-slate-500">{{ __('No cholesterol records available.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            <div class="mb-3 flex flex-wrap items-center gap-3">
                <label for="cholesterolParticipantSelect" class="text-sm font-medium text-slate-700">{{ __('Select Participant for Trend') }}</label>
                <select id="cholesterolParticipantSelect" class="form-select max-w-md">
                    @foreach($cholesterolParticipants as $item)
                        <option value="{{ $item['participant_id'] }}" @selected((int) $defaultTrendParticipantId === (int) $item['participant_id'])>
                            {{ $item['participant_name'] }} ({{ $item['participant_no'] }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="h-80">
                <canvas id="cholesterolTrendChart"></canvas>
            </div>
            <p class="mt-2 text-xs text-slate-500" id="cholesterolTrendEmptyState"></p>
        </div>
    </div>
</x-app-layout>
