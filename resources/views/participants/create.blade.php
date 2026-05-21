<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Register Participant') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('participants.store') }}">
            @csrf
            @include('participants._form')
        </form>
    </div>
</x-app-layout>
