<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Newsletter') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Display all subscribers and send email to all or selected recipients.') }}</p>
            </div>
            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                {{ __('Total') }}: {{ $subscribers->count() }}
            </span>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('newsletter-subscribers.index') }}" class="panel-card p-4">
        <div class="grid gap-3 md:grid-cols-4">
            <input type="text" id="participantSearch" value="{{ $search }}" class="form-input md:col-span-3" placeholder="{{ __('Search by name or email') }}">
            <button class="filter-btn" type="submit">{{ __('Search') }}</button>
        </div>
    </form>

    <form method="POST" action="{{ route('newsletter-subscribers.send-email') }}" class="mt-6 grid gap-6 xl:grid-cols-3">
        @csrf

        <section class="panel-card p-5 xl:col-span-2">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-slate-800">{{ __('Participant List') }}</h2>
                <div class="text-sm text-slate-500">
                    {{ __('Selected') }}: <span id="selectedSubscriberCount" class="font-bold text-slate-800">0</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                             <th>No.</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Subscribed At') }}</th>
                    </thead>
                    <tbody id="participantTableBody">
                        @php($oldSelectedIds = collect(old('subscriber_ids', []))->map(fn ($id) => (int) $id)->all())
                     <tr>
                    <td colspan="4" class="text-center text-slate-500">
                    Select a test session
                    </td>
                </tr>
                    </tbody>
                </table>
            </div>

            @error('subscriber_ids')
                <p class="mt-3 text-sm text-rose-700">{{ $message }}</p>
            @enderror
            @error('subscriber_ids.*')
                <p class="mt-1 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </section>

        <section class="panel-card p-5">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Compose Email') }}</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-slate-700">{{ __('Recipients') }}</p>
                    <div class="mt-2 space-y-2">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="recipient_mode" value="all" class="text-teal-600 focus:ring-teal-500" @checked(old('recipient_mode', 'all') === 'all')>
                            <span>{{ __('Send to all subscribers') }}</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="recipient_mode" value="selected" class="text-teal-600 focus:ring-teal-500" @checked(old('recipient_mode') === 'selected')>
                            <span>{{ __('Send only to selected subscribers') }}</span>
                        </label>
                    </div>
                    @error('recipient_mode')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                <div>
    <label class="mb-1 block text-sm font-semibold text-slate-700">
        {{ __('Test Session') }}
    </label>

    <select id="testSessionSelect" name="test_session_id" class="form-input w-full">
        <option value="">Select Test Session</option>

        @foreach($sessions as $session)
            <option value="{{ $session->id }}">
                {{ $session->session_code }}
            </option>
        @endforeach
    </select>
</div>
                    <label class="text-sm font-medium text-slate-700" for="subject">{{ __('Subject') }}</label>
                    <input id="subject" name="subject" type="text" class="form-input mt-1" value="{{ old('subject') }}" maxlength="180" required>
                    @error('subject')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700" for="message">{{ __('Message') }}</label>
                    <textarea id="message" name="message" rows="10" class="form-input mt-1" maxlength="5000" required>{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">{{ __('You can write multiple lines. The email keeps line breaks.') }}</p>
                    @error('message')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    {{ __('Send Newsletter Email') }}
                </button>

            </div>
        </section>
    </form>
    <script>

document.addEventListener('DOMContentLoaded', function () {
    let allParticipants = [];

    const sessionSelect = document.getElementById('testSessionSelect');

    const tableBody = document.getElementById('participantTableBody');

    sessionSelect.addEventListener('change', async function () {

        const sessionId = this.value;

        if (!sessionId) {

            tableBody.innerHTML = `
                <tr>
                    <td colspan="4">
                        Select a test session
                    </td>
                </tr>
            `;

            return;
        }

        const response = await fetch(
            `/newsletter/test-session/${sessionId}/participants`
        );

        const participants = await response.json();

        allParticipants = participants;

        const renderParticipants = (participants) => {

        tableBody.innerHTML = participants.map((participant, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${participant.name}</td>
            <td>${participant.email}</td>
            <td>${participant.session}</td>
        </tr>
    `).join('');

};

renderParticipants(participants);
const searchInput = document.getElementById('participantSearch');

searchInput.addEventListener('input', function () {

    const keyword = this.value.toLowerCase();

    const filtered = allParticipants.filter(participant => {

        return participant.name.toLowerCase().includes(keyword)
            || participant.email.toLowerCase().includes(keyword);

    });

    renderParticipants(filtered);

});
    });

});

</script>
<style>
.datatable-top {
    display: none !important;
}
</style>

</x-app-layout>
