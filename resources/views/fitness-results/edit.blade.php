<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Edit Fitness Result') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('fitness-results.update', $fitnessResult) }}">
            @csrf
            @method('PUT')
            @include('fitness-results._form', ['fitnessResult' => $fitnessResult])
        </form>
    </div>
</x-app-layout>
