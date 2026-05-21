<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Edit User') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('users._form', ['user' => $user])
        </form>
    </div>
</x-app-layout>
