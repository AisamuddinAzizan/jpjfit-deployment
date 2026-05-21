<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'JPJFit') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/jpjfit_logo.png') }}">
        <link rel="shortcut icon" href="{{ asset('images/jpjfit_logo.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-bg min-h-screen antialiased">
        <div id="pageLoader" class="page-loader">
            <span class="loader-dot"></span>
            <span class="loader-dot"></span>
            <span class="loader-dot"></span>
        </div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed right-4 top-4 z-20">
                <form method="POST" action="{{ route('language.switch') }}">
                    @csrf
                    <select name="locale" class="navbar-select" onchange="this.form.submit()">
                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                        <option value="ms" @selected(app()->getLocale() === 'ms')>{{ __('Malay') }}</option>
                    </select>
                </form>
            </div>

            <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/20 bg-white/90 shadow-2xl backdrop-blur md:grid-cols-2">
                <div class="hidden bg-[radial-gradient(circle_at_top,_#0f766e,_#0b2d45)] p-10 text-white md:block">
                    <p class="text-xs uppercase tracking-[0.4em] text-teal-100">{{ __('Official Fitness Management System') }}</p>
                    <div class="mt-4 flex items-center gap-3">
                        <img src="{{ asset('images/jpjfit_logo.png') }}" alt="{{ config('app.name', 'JPJFit') }} logo" class="rounded-xl bg-white/10 p-1 object-contain" style="width: 100px; height: auto;">
                        <h1 class="text-4xl font-extrabold leading-tight">JPJFit</h1>
                    </div>
                    <p class="mt-5 text-sm text-teal-100">{{ __('Integrated UKJK test scheduling, health records, and certification workflow for JPJ and KKM officers.') }}</p>
                    <div class="mt-10 rounded-2xl border border-white/20 bg-white/10 p-5 text-sm">
                        <p class="font-semibold">{{ __('Secure access for:') }}</p>
                        <ul class="mt-3 space-y-2 text-teal-50">
                            <li>{{ __('System Admin') }}</li>
                            <li>{{ __('JPJ Officer') }}</li>
                            <li>{{ __('Health Officer (KKM)') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="p-6 sm:p-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
