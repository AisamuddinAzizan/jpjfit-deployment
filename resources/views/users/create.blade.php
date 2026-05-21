<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Create User') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._form')
        </form>
    </div>
</x-app-layout>
