<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Mail Provider Settings') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Configure Gmail, Outlook, Mailtrap, or SMTP from admin dashboard.') }}</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('mail-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="panel-card p-5">
            <h2 class="text-lg font-bold text-slate-800">{{ __('Active Mailer') }}</h2>
            <div class="mt-4">
                <label class="text-sm font-medium text-slate-700" for="active_mailer">{{ __('Choose Provider') }}</label>
                <select id="active_mailer" name="settings[active_mailer]" class="form-select" required>
                    @foreach(($mailerOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" @selected(old('settings.active_mailer', $settings['active_mailer'] ?? 'log') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('settings.active_mailer') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </section>

        @foreach(($groups ?? []) as $prefix => $group)
            <section class="panel-card p-5">
                <h2 class="text-lg font-bold text-slate-800">{{ __($group['title']) }}</h2>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @foreach(($fieldMap ?? []) as $field => $label)
                        @php
                            $key = $prefix.'_'.$field;
                            $name = 'settings.'.$key;
                            $value = old($name, $settings[$key] ?? '');
                        @endphp
                        <div>
                            <label class="text-sm font-medium text-slate-700" for="{{ $key }}">{{ __($label) }}</label>
                            <input
                                id="{{ $key }}"
                                name="settings[{{ $key }}]"
                                type="{{ $field === 'port' ? 'number' : ($field === 'from_address' ? 'email' : 'text') }}"
                                value="{{ $value }}"
                                class="form-input"
                            >
                            @error($name) <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    @php
                        $passwordKey = $group['passwordKey'];
                        $passwordName = 'settings.'.$passwordKey;
                    @endphp
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700" for="{{ $passwordKey }}">{{ __('Password / App Password') }}</label>
                        <input
                            id="{{ $passwordKey }}"
                            name="settings[{{ $passwordKey }}]"
                            type="password"
                            class="form-input"
                            placeholder="{{ __('Leave blank to keep existing password') }}"
                        >
                        @error($passwordName) <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        @endforeach

        <section class="panel-card p-5">
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="btn-primary">{{ __('Save Mail Settings') }}</button>
                <p class="text-sm text-slate-500">{{ __('After saving, new emails will use the selected provider.') }}</p>
            </div>
        </section>
    </form>
</x-app-layout>
