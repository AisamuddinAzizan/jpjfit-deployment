<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        <h2 class="text-2xl font-extrabold text-slate-800">{{ __('Sign in to JPJFit') }}</h2>
        <p class="mt-2 text-sm text-slate-500">{{ __('Use your official account to manage UKJK activities.') }}</p>

        <x-auth-session-status class="mb-4 mt-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-slate-700">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-input" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500">
                    {{ __('Remember me') }}
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-medium text-teal-700 hover:text-teal-900">{{ __('Forgot password?') }}</a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full justify-center">{{ __('Log In') }}</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            {{ __('No account yet?') }}
            <a href="{{ route('register') }}" class="font-semibold text-teal-700 hover:text-teal-900">{{ __('Register') }}</a>
        </p>
    </div>
</x-guest-layout>
