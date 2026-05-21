<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Create UKJK Test Session') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('test-sessions.store') }}">
            @csrf
            @include('test-sessions._form')
        </form>
    </div>
</x-app-layout>
