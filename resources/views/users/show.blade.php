<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('User Details') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Name') }}</dt><dd class="font-semibold text-slate-800">{{ $user->name }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Email') }}</dt><dd class="font-semibold text-slate-800">{{ $user->email }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Phone') }}</dt><dd class="font-semibold text-slate-800">{{ $user->phone ?? '-' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Department') }}</dt><dd class="font-semibold text-slate-800">{{ $user->department ?? '-' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Role') }}</dt><dd class="font-semibold text-slate-800">{{ ucwords(str_replace('_', ' ', $user->roles->first()->name ?? __('N/A'))) }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">{{ __('Status') }}</dt><dd class="font-semibold text-slate-800">{{ $user->is_active ? __('Active') : __('Inactive') }}</dd></div>
        </dl>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('users.edit', $user) }}" class="btn-primary">{{ __('Edit User') }}</a>
            <a href="{{ route('users.index') }}" class="btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>
</x-app-layout>
