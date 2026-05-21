<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Edit Health Record') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('health-records.update', $healthRecord) }}">
            @csrf
            @method('PUT')
            @include('health-records._form', ['healthRecord' => $healthRecord])
        </form>
    </div>
</x-app-layout>
