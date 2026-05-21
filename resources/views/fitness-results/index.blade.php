<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Fitness Test Results') }}</h1>
            <a href="{{ route('fitness-results.create') }}" class="btn-primary">{{ __('Record Result') }}</a>
        </div>
    </x-slot>

    <div class="panel-card p-5">
        <form method="GET" action="{{ route('fitness-results.index') }}" class="grid gap-3 md:grid-cols-3">
            <input type="text" name="search" class="form-input md:col-span-2" placeholder="{{ __('Search participant') }}" value="{{ $search }}">
            <button class="filter-btn" type="submit">{{ __('Search') }}</button>
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
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ $result->participant?->full_name }}</td>
                            <td>{{ $result->testSession?->session_code }}</td>
                            <td>{{ $result->total_score }}</td>
                            <td>{{ __($result->classification) }}</td>
                            <td>
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $result->result_status === 'Pass' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ __($result->result_status) }}</span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('fitness-results.show', $result) }}" class="text-sm font-semibold text-sky-700">{{ __('View') }}</a>
                                <a href="{{ route('fitness-results.edit', $result) }}" class="ml-3 text-sm font-semibold text-teal-700">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('fitness-results.destroy', $result) }}" class="ml-3 inline" onsubmit="return confirm('{{ __('Delete result?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-rose-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-500">{{ __('No results found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
