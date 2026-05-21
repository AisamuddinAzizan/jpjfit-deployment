<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        <h2 class="text-2xl font-extrabold text-slate-800">{{ __('Create JPJFit Account') }}</h2>
        <p class="mt-2 text-sm text-slate-500">{{ __('Registration creates a JPJ Officer account by default.') }}</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="text-sm font-medium text-slate-700">{{ __('Full Name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-input" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <label for="email" class="text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-input" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="phone" class="text-sm font-medium text-slate-700">{{ __('Phone') }}</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-input" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>
                <div>
                    <label for="department" class="text-sm font-medium text-slate-700">{{ __('Department') }}</label>
                    <input id="department" type="text" name="department" value="{{ old('department') }}" class="form-input" />
                    <x-input-error :messages="$errors->get('department')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-slate-700">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="form-input" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-medium text-slate-700">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <button type="submit" class="btn-primary w-full justify-center">{{ __('Register') }}</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            {{ __('Already registered?') }}
            <a href="{{ route('login') }}" class="font-semibold text-teal-700 hover:text-teal-900">{{ __('Sign in') }}</a>
        </p>
    </div>
</x-guest-layout>
