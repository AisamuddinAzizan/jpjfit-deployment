<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('User Management') }}</h1>
            <a href="{{ route('users.create') }}" class="btn-primary">{{ __('Create User') }}</a>
        </div>
    </x-slot>

    <div class="panel-card p-5">
        <form method="GET" action="{{ route('users.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $search }}" class="form-input md:col-span-2" placeholder="{{ __('Search name or email') }}">
            <select name="role" class="form-select">
                <option value="">{{ __('All Roles') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected($selectedRole === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                @endforeach
            </select>
            <button class="filter-btn" type="submit">{{ __('Filter') }}</button>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $user->roles->first()->name ?? __('N/A'))) }}</td>
                            <td>
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('users.show', $user) }}" class="text-sm font-semibold text-sky-700">{{ __('View') }}</a>
                                <a href="{{ route('users.edit', $user) }}" class="ml-3 text-sm font-semibold text-teal-700">{{ __('Edit') }}</a>
                                <form class="ml-3 inline" method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('{{ __('Delete this user?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-rose-700">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-slate-500">{{ __('No users found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
