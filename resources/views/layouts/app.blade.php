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
    <body class="min-h-screen bg-slate-100 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
        <div id="pageLoader" class="page-loader">
            <span class="loader-dot"></span>
            <span class="loader-dot"></span>
            <span class="loader-dot"></span>
        </div>

        <div class="flex min-h-screen">
            <aside class="fixed inset-y-0 left-0 z-40 w-72 transform dashboard-bg p-5 text-slate-100 shadow-2xl transition duration-300 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <div class="mb-6 flex items-center justify-between lg:justify-start">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/jpjfit_logo.png') }}" alt="{{ config('app.name', 'JPJFit') }} logo" class="rounded-lg bg-white/10 p-1 object-contain" style="width: 100px; height: auto;">
                        <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-teal-200">{{ __('Government Fitness Portal') }}</p>
                        <h1 class="text-2xl font-extrabold">JPJFit</h1>
                        </div>
                    </div>
                    <button class="rounded-md border border-white/30 px-2 py-1 text-xs lg:hidden" @click="sidebarOpen = false">{{ __('Close') }}</button>
                </div>

                <nav class="space-y-2 text-sm">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('dashboard') ? 'bg-white/20 font-semibold' : '' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z" /></svg>
                        <span>{{ __('Dashboard') }}</span>
                    </a>

                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('users.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('users.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m8-11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>{{ __('User Management') }}</span>
                        </a>
                        <a href="{{ route('landing-content.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('landing-content.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h6.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V18a2 2 0 01-2 2z" /></svg>
                            <span>{{ __('Landing Content') }}</span>
                        </a>
                        <a href="{{ route('mail-settings.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('mail-settings.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 10H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2z" /></svg>
                            <span>{{ __('Mail Settings') }}</span>
                        </a>
                        <a href="{{ route('newsletter-subscribers.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('newsletter-subscribers.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10m-7 6h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>{{ __('Newsletter') }}</span>
                        </a>
                    @endif
                    @if(auth()->user()->hasRole('admin'))
    <a href="{{ route('test-sessions.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('test-sessions.*') ? 'bg-white/20 font-semibold' : '' }}">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m2 10H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z" />
        </svg>

        <span>{{ __('Test Sessions') }}</span>
    </a>
                    @endif
             @if(auth()->user()->hasRole('admin'))
    <a href="{{ route('participants.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('participants.*') ? 'bg-white/20 font-semibold' : '' }}">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h7m-7-4a4 4 0 11-8 0 4 4 0 018 0zm-8 14v-2a4 4 0 014-4h4" />
        </svg>

        <span>{{ __('Participants') }}</span>
    </a>
                    @endif
            @if(auth()->user()->hasRole('jpj_officer'))
    <a href="{{ route('fitness-results.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('fitness-results.*') ? 'bg-white/20 font-semibold' : '' }}">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h4l2-5 4 10 2-5h2" />
        </svg>

        <span>{{ __('Fitness Results') }}</span>
    </a>
            @endif   

                    @if(auth()->user()->hasAnyRole(['health_officer']))
                        <a href="{{ route('health-records.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('health-records.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8m6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Health Records') }}</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasAnyRole(['admin', 'jpj_officer', 'health_officer']))
                        <a href="{{ route('certificates.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('certificates.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 7l-8 4-8-4V5l8-4 8 4v12z" /></svg>
                            <span>{{ __('Certificates') }}</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('reports.*') ? 'bg-white/20 font-semibold' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span>{{ __('Reports') }}</span>
                        </a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/15 {{ request()->routeIs('profile.*') ? 'bg-white/20 font-semibold' : '' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>{{ __('Profile') }}</span>
                    </a>
                </nav>

                <div class="mt-8 rounded-2xl bg-white/10 p-4 text-xs text-teal-50">
                    <p class="font-semibold">{{ __('Signed in as') }}</p>
                    <p class="mt-1 text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-1 uppercase tracking-wider text-teal-100">{{ str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? 'no role') }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button class="w-full rounded-lg bg-amber-400 px-3 py-2 text-xs font-semibold text-slate-900 transition hover:bg-amber-300">{{ __('Logout') }}</button>
                    </form>
                </div>
            </aside>

            <div class="flex min-h-screen w-full flex-col lg:pl-72">
                @php
                    $segments = request()->segments();
                    $path = '';
                    $breadcrumbs = [
                        ['label' => __('Dashboard'), 'url' => route('dashboard')],
                    ];

                    foreach ($segments as $segment) {
                        $path .= '/'.$segment;

                        if ($segment === 'dashboard') {
                            continue;
                        }

                        $breadcrumbs[] = [
                            'label' => is_numeric($segment) ? '#'.$segment : (string) str($segment)->replace(['-', '_'], ' ')->title(),
                            'url' => url($path),
                        ];
                    }
                @endphp

                <header class="app-header sticky top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur">
                    <div class="space-y-3 px-4 py-3 md:px-8">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <button class="rounded-md border border-slate-300 px-2 py-1 text-xs lg:hidden" @click="sidebarOpen = true">{{ __('Menu') }}</button>
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-500">{{ __('Road Transport Department') }}</p>
                                    <h2 class="text-lg font-bold text-slate-800">{{ config('app.name', 'JPJFit') }} {{ __('Control Center') }}</h2>
                                </div>
                            </div>
                            <div class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-teal-700">
                                {{ now()->format('d M Y') }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs sm:text-sm">
                                @foreach($breadcrumbs as $index => $breadcrumb)
                                    @if($index > 0)
                                        <span class="text-slate-400">/</span>
                                    @endif

                                    @if($loop->last)
                                        <span class="font-semibold text-teal-700">{{ $breadcrumb['label'] }}</span>
                                    @else
                                        <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['label'] }}</a>
                                    @endif
                                @endforeach
                            </nav>

                            <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
                                <form method="GET" action="{{ url()->current() }}" class="w-full sm:w-64">
                                    <input type="text" name="search" value="{{ request('search') }}" class="navbar-input" placeholder="{{ __('Search this page') }}">
                                </form>

                                <button type="button" id="themeToggle" class="navbar-action-btn" title="{{ __('Toggle theme') }}">
                                    <svg id="themeIconMoon" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    <svg id="themeIconSun" class="hidden h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0L16.95 7.05M7.05 16.95l-1.414 1.414M12 16a4 4 0 100-8 4 4 0 000 8z" />
                                    </svg>
                                </button>

                                <form method="POST" action="{{ route('language.switch') }}">
                                    @csrf
                                    <select name="locale" class="navbar-select" onchange="this.form.submit()">
                                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                                        <option value="ms" @selected(app()->getLocale() === 'ms')>{{ __('Malay') }}</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 md:px-8 md:py-8">
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    @if(session('success'))
                        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
