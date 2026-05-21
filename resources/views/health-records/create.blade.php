<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Add Health Record') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('health-records.store') }}">
            @csrf
            @include('health-records._form')
        </form>
    </div>
</x-app-layout>
