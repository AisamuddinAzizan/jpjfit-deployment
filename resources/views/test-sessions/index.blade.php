<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Test Session Management') }}</h1>
            <a href="{{ route('test-sessions.create') }}" class="btn-primary">{{ __('Create Session') }}</a>
        </div>
    </x-slot>

    <div class="panel-card p-5">
        <form method="GET" action="{{ route('test-sessions.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $search }}" class="form-input md:col-span-2" placeholder="{{ __('Search title, code, location') }}">
            <select name="status" class="form-select">
                <option value="">{{ __('All Status') }}</option>
                @foreach(['scheduled', 'ongoing', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ __(ucfirst($status)) }}</option>
                @endforeach
            </select>
            <button class="filter-btn" type="submit">{{ __('Filter') }}</button>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Location') }}</th>
                        <th>{{ __('Participants') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>{{ $session->session_code }}</td>
                            <td>{{ $session->title }}</td>
                            <td>{{ $session->session_date?->format('d M Y') }}</td>
                            <td>{{ $session->location }}</td>
                            <td>{{ $session->participants_count }}</td>
                            <td><span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-700">{{ __(ucfirst($session->status)) }}</span></td>
                            <td class="text-right">
                                <a href="{{ route('test-sessions.show', $session) }}" class="text-sm font-semibold text-sky-700">{{ __('View') }}</a>
                                <a href="{{ route('test-sessions.edit', $session) }}" class="ml-3 text-sm font-semibold text-teal-700">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('test-sessions.destroy', $session) }}" class="ml-3 inline" onsubmit="return confirm('{{ __('Delete session?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-rose-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-slate-500">{{ __('No sessions found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
