<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Newsletter Subscribers') }}</h1>
                <p class="text-sm text-slate-500">{{ __('Display all subscribers and send email to all or selected recipients.') }}</p>
            </div>
            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                {{ __('Total') }}: {{ $subscribers->count() }}
            </span>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('newsletter-subscribers.index') }}" class="panel-card p-4">
        <div class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $search }}" class="form-input md:col-span-3" placeholder="{{ __('Search by name or email') }}">
            <button class="filter-btn" type="submit">{{ __('Search') }}</button>
        </div>
    </form>

    <form method="POST" action="{{ route('newsletter-subscribers.send-email') }}" class="mt-6 grid gap-6 xl:grid-cols-3">
        @csrf

        <section class="panel-card p-5 xl:col-span-2">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-slate-800">{{ __('Subscribers List') }}</h2>
                <div class="text-sm text-slate-500">
                    {{ __('Selected') }}: <span id="selectedSubscriberCount" class="font-bold text-slate-800">0</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>
                                <input id="selectAllSubscribers" type="checkbox" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                            </th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Subscribed At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($oldSelectedIds = collect(old('subscriber_ids', []))->map(fn ($id) => (int) $id)->all())
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="subscriber_ids[]"
                                        value="{{ $subscriber->id }}"
                                        class="subscriber-checkbox rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                        @checked(in_array($subscriber->id, $oldSelectedIds, true))
                                    >
                                </td>
                                <td>{{ $subscriber->name ?: '-' }}</td>
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->subscribed_at?->format('d M Y H:i') ?? $subscriber->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate-500">{{ __('No subscribers found.') }}</td>
                            </tr>
                        @endforelse
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

    <select name="test_session_id" class="form-input w-full">
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
        (function () {
            const master = document.getElementById('selectAllSubscribers');
            const checkboxes = Array.from(document.querySelectorAll('.subscriber-checkbox'));
            const counter = document.getElementById('selectedSubscriberCount');

            const updateSelectedCounter = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                if (counter) {
                    counter.textContent = String(selected);
                }

                if (master) {
                    master.checked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
                }
            };

            if (master) {
                master.addEventListener('change', () => {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = master.checked;
                    });
                    updateSelectedCounter();
                });
            }

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateSelectedCounter);
            });

            updateSelectedCounter();
        }());
    </script>
</x-app-layout>
