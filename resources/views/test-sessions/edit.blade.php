<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Edit Test Session') }}</h1>
    </x-slot>

    <div class="panel-card p-5">
        <form method="POST" action="{{ route('test-sessions.update', $testSession) }}">
            @csrf
            @method('PUT')
            @include('test-sessions._form', ['testSession' => $testSession])
        </form>
    </div>
</x-app-layout>
