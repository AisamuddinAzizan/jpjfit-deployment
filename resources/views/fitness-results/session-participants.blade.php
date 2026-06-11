<x-app-layout>

    <x-slot name="header">
        <h1 class="text-2xl font-bold">
            Participants - {{ $session->session_code }}
        </h1>
    </x-slot>

    <div class="panel-card p-5">

        <table class="min-w-full border">
            <thead>
                <tr>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Participant No</th>
                    <th class="border p-2">Name</th>
                </tr>
            </thead>

            <tbody>

                @foreach($participants as $item)

                    <tr>

                        <td class="border p-2 text-center">

                            @if($item['completed'])
                                ✅
                            @else
                                ❌
                            @endif

                        </td>

                        <td class="border p-2">
                            {{ $item['participant']->participant_no }}
                        </td>

                        <td class="border p-2">
                            {{ $item['participant']->full_name }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</x-app-layout>