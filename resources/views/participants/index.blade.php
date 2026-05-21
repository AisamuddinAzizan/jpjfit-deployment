<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Participant Management') }}</h1>
            <a href="{{ route('participants.create') }}" class="btn-primary">{{ __('Register Participant') }}</a>
        </div>
    </x-slot>

    <div class="panel-card p-5">
        <form method="GET" action="{{ route('participants.index') }}" class="grid gap-3 md:grid-cols-3">
            <input type="text" name="search" class="form-input md:col-span-2" placeholder="{{ __('Search by name, participant no, IC, agency') }}" value="{{ $search }}">
            <button class="filter-btn" type="submit">{{ __('Search') }}</button>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('No') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('IC No') }}</th>
                        <th>{{ __('Agency') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $participant)
                        <tr>
                            <td>{{ $participant->participant_no }}</td>
                            <td>{{ $participant->full_name }}</td>
                            <td>{{ $participant->ic_no }}</td>
                            <td>{{ $participant->agency ?? '-' }}</td>
                            <td>
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $participant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $participant->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('participants.show', $participant) }}" class="text-sm font-semibold text-sky-700">{{ __('View') }}</a>
                                <a href="{{ route('participants.edit', $participant) }}" class="ml-3 text-sm font-semibold text-teal-700">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('participants.destroy', $participant) }}" class="ml-3 inline" onsubmit="return confirm('{{ __('Delete participant?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-rose-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-500">{{ __('No participants found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
