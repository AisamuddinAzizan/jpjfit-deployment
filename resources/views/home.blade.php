<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JPJFit - Fitness Monitoring System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/jpjfit_logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .landing-hero {
            background-image:
                radial-gradient(circle at 12% 20%, rgba(56, 189, 248, 0.26), transparent 36%),
                radial-gradient(circle at 85% 10%, rgba(20, 184, 166, 0.28), transparent 35%),
                linear-gradient(120deg, rgba(11, 45, 69, 0.94), rgba(15, 118, 110, 0.88));
            background-size: cover;
            background-position: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.55);
        }

        .step-badge {
            width: 2.2rem;
            height: 2.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #0f766e, #0b2d45);
        }

        .floating {
            animation: floatY 3.5s ease-in-out infinite;
        }

        @keyframes floatY {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .preview-bg-1 {
            background-image: linear-gradient(135deg, #0f766e, #0b2d45);
        }

        .preview-bg-2 {
            background-image: linear-gradient(135deg, #1d4ed8, #0e7490);
        }

        .preview-bg-3 {
            background-image: linear-gradient(135deg, #0f172a, #334155);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="#hero" class="flex items-center gap-3">
                <img src="{{ asset('images/jpjfit_logo.png') }}" alt="JPJFit logo" class="h-10 w-auto object-contain">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-teal-700">JPJFit</p>
                    <p class="text-sm font-semibold text-slate-800">Fitness Monitoring System</p>
                </div>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                <a href="#overview" class="hover:text-teal-700">Overview</a>
                <a href="#features" class="hover:text-teal-700">Features</a>
                <a href="#workflow" class="hover:text-teal-700">Workflow</a>
                <a href="#faq" class="hover:text-teal-700">FAQ</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="btn-secondary">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">Register</a>
                @endif
            </div>
        </div>
    </header>

    <section id="hero" class="landing-hero relative overflow-hidden">
        <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 py-20 sm:px-6 md:grid-cols-2 md:py-28 lg:px-8">
            <div class="text-white" data-aos="fade-up">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-teal-100">Digital Fitness Oversight</p>
                <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl">JPJFit - Fitness Monitoring System</h1>
                <p class="mt-6 max-w-xl text-base text-slate-100 sm:text-lg">
                    A modern platform to manage fitness tests, monitor readiness, generate reports, and streamline certification for JPJ and KKM teams.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="btn-primary">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary border-white/40 bg-white/10 text-white hover:bg-white/20">Register</a>
                    @endif
                </div>
            </div>

            <div class="floating grid gap-4" data-aos="zoom-in" data-aos-delay="120">
                <div class="glass-card rounded-2xl p-5 shadow-xl">
                    <p class="text-sm font-semibold text-teal-700">Live Stats</p>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-slate-100 p-3 text-center">
                            <p class="text-xs text-slate-500">Participants</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900" data-counter-target="{{ $stats['total_participants'] }}">0</p>
                        </div>
                        <div class="rounded-xl bg-slate-100 p-3 text-center">
                            <p class="text-xs text-slate-500">Sessions</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900" data-counter-target="{{ $stats['total_test_sessions'] }}">0</p>
                        </div>
                        <div class="rounded-xl bg-slate-100 p-3 text-center">
                            <p class="text-xs text-slate-500">Pass Rate</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900" data-counter-target="{{ (int) round($stats['pass_rate']) }}">0</p>
                        </div>
                    </div>
                </div>
                <div class="glass-card rounded-2xl p-5 shadow-xl">
                    <p class="text-sm font-semibold text-teal-700">Training Snapshot</p>
                    <p class="mt-2 text-sm text-slate-600">Unified records for test sessions, health screening, fitness scoring, and certification workflow.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="overview" class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8" data-aos="fade-up">
        <div class="grid gap-8 rounded-3xl bg-white p-8 shadow-lg md:grid-cols-2">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">System Overview</h2>
                <p class="mt-4 text-slate-600">
                    JPJFit centralizes participant records, test schedules, health data, and performance outcomes in one secure dashboard for better decisions and faster operations.
                </p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-teal-50 p-4">
                    <p class="text-sm font-semibold text-teal-800">Role-Based Access</p>
                    <p class="mt-2 text-sm text-slate-600">Admin, JPJ Officer, and Health Officer workflows are separated and secure.</p>
                </div>
                <div class="rounded-2xl bg-sky-50 p-4">
                    <p class="text-sm font-semibold text-sky-800">Evidence Ready</p>
                    <p class="mt-2 text-sm text-slate-600">Generate reports and certificates quickly with structured digital records.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="bg-slate-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-extrabold text-slate-900" data-aos="fade-up">Key Features</h2>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['title' => 'Participant Management', 'text' => 'Register, track and update participant profiles with complete details.'],
                    ['title' => 'Session Scheduling', 'text' => 'Create and manage UKJK test sessions with attendance and status tracking.'],
                    ['title' => 'Auto Fitness Scoring', 'text' => 'Calculate classification and pass/fail outcomes automatically from test input.'],
                    ['title' => 'Reports & Certificates', 'text' => 'Export PDF/CSV reports and issue fitness certificates in minutes.'],
                ] as $index => $feature)
                    <article class="rounded-2xl bg-white p-5 shadow-md" data-aos="fade-up" data-aos-delay="{{ 80 * ($index + 1) }}">
                        <h3 class="text-lg font-bold text-slate-900">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $feature['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="workflow" class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="text-center text-3xl font-extrabold text-slate-900" data-aos="fade-up">How The System Works</h2>
        <div class="mt-10 grid gap-6 md:grid-cols-4">
            @foreach([
                ['step' => '1', 'title' => 'Register Participant', 'text' => 'Capture official participant profile and assign session.'],
                ['step' => '2', 'title' => 'Conduct Tests', 'text' => 'Record health checks and UKJK fitness test measurements.'],
                ['step' => '3', 'title' => 'Auto Evaluate', 'text' => 'System computes score, classification and result status.'],
                ['step' => '4', 'title' => 'Issue Outputs', 'text' => 'Generate reports, dashboards and certificate documents.'],
            ] as $index => $item)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" data-aos="fade-up" data-aos-delay="{{ 90 * ($index + 1) }}">
                    <span class="step-badge">{{ $item['step'] }}</span>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6" data-aos="fade-right">
                    <h3 class="text-2xl font-extrabold text-slate-900">Fitness Test Preview</h3>
                    <p class="mt-3 text-sm text-slate-600">Push-ups, sit-ups, flexibility, shuttle run, and 2.4km run results are captured and scored consistently.</p>
                    <ul class="mt-4 space-y-2 text-sm text-slate-700">
                        <li>- Structured metrics input</li>
                        <li>- Automatic classification</li>
                        <li>- Pass/fail outcome in real time</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6" data-aos="fade-left">
                    <h3 class="text-2xl font-extrabold text-slate-900">Statistics Counters</h3>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-xl bg-white p-3">
                            <p class="text-xs text-slate-500">Participants</p>
                            <p class="mt-1 text-2xl font-extrabold text-teal-700" data-counter-target="{{ $stats['total_participants'] }}">0</p>
                        </div>
                        <div class="rounded-xl bg-white p-3">
                            <p class="text-xs text-slate-500">Sessions</p>
                            <p class="mt-1 text-2xl font-extrabold text-teal-700" data-counter-target="{{ $stats['total_test_sessions'] }}">0</p>
                        </div>
                        <div class="rounded-xl bg-white p-3">
                            <p class="text-xs text-slate-500">Pass Rate</p>
                            <p class="mt-1 text-2xl font-extrabold text-teal-700">{{ $stats['pass_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="text-center text-3xl font-extrabold text-slate-900" data-aos="fade-up">Dashboard Preview</h2>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            <div class="preview-bg-1 overflow-hidden rounded-2xl border border-slate-200 p-4 text-white shadow" data-aos="zoom-in">
                <p class="text-sm font-semibold">Participant Distribution</p>
                <div class="mt-5 flex h-28 items-end gap-2">
                    <span class="w-5 rounded-t bg-white/80" style="height: 45%"></span>
                    <span class="w-5 rounded-t bg-white/80" style="height: 62%"></span>
                    <span class="w-5 rounded-t bg-white/80" style="height: 78%"></span>
                    <span class="w-5 rounded-t bg-white/80" style="height: 58%"></span>
                    <span class="w-5 rounded-t bg-white/80" style="height: 86%"></span>
                </div>
            </div>
            <div class="preview-bg-2 overflow-hidden rounded-2xl border border-slate-200 p-4 text-white shadow" data-aos="zoom-in" data-aos-delay="120">
                <p class="text-sm font-semibold">Session Completion</p>
                <div class="mt-5 space-y-2">
                    <div class="h-2 rounded-full bg-white/30">
                        <div class="h-2 w-4/5 rounded-full bg-white"></div>
                    </div>
                    <div class="h-2 rounded-full bg-white/30">
                        <div class="h-2 w-3/5 rounded-full bg-white"></div>
                    </div>
                    <div class="h-2 rounded-full bg-white/30">
                        <div class="h-2 w-5/6 rounded-full bg-white"></div>
                    </div>
                    <div class="h-2 rounded-full bg-white/30">
                        <div class="h-2 w-2/3 rounded-full bg-white"></div>
                    </div>
                </div>
            </div>
            <div class="preview-bg-3 overflow-hidden rounded-2xl border border-slate-200 p-4 text-white shadow" data-aos="zoom-in" data-aos-delay="220">
                <p class="text-sm font-semibold">Certificate Pipeline</p>
                <div class="mt-4 flex items-center justify-between rounded-xl bg-white/10 p-3">
                    <img src="{{ asset('images/jpjfit_logo.png') }}" alt="JPJFit" class="h-10 w-auto rounded bg-white p-1">
                    <span class="rounded-full bg-emerald-400/80 px-2 py-1 text-xs font-bold text-slate-900">READY</span>
                </div>
                <div class="mt-3 h-2 rounded-full bg-white/20">
                    <div class="h-2 w-4/5 rounded-full bg-emerald-300"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-extrabold text-slate-900" data-aos="fade-up">Testimonials</h2>
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach([
                    ['quote' => 'JPJFit helped us reduce manual paperwork and report turnaround time drastically.', 'name' => 'Operations Lead, JPJ'],
                    ['quote' => 'Health data and fitness performance are now easier to monitor in one place.', 'name' => 'Health Officer, KKM'],
                    ['quote' => 'The dashboard gives management instant visibility on readiness and outcomes.', 'name' => 'Admin Coordinator'],
                ] as $index => $item)
                    <blockquote class="rounded-2xl bg-white p-5 shadow" data-aos="fade-up" data-aos-delay="{{ 90 * ($index + 1) }}">
                        <p class="text-sm text-slate-600">"{{ $item['quote'] }}"</p>
                        <footer class="mt-4 text-sm font-semibold text-slate-900">- {{ $item['name'] }}</footer>
                    </blockquote>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faq" class="mx-auto w-full max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="text-center text-3xl font-extrabold text-slate-900" data-aos="fade-up">FAQ Preview</h2>
        <div class="mt-8 space-y-4">
            <details class="rounded-xl border border-slate-200 bg-white p-4" data-aos="fade-up">
                <summary class="cursor-pointer font-semibold text-slate-900">Who can access JPJFit?</summary>
                <p class="mt-2 text-sm text-slate-600">System Admin, JPJ Officers, and Health Officers with role-based permissions.</p>
            </details>
            <details class="rounded-xl border border-slate-200 bg-white p-4" data-aos="fade-up" data-aos-delay="80">
                <summary class="cursor-pointer font-semibold text-slate-900">Can reports be exported?</summary>
                <p class="mt-2 text-sm text-slate-600">Yes, reports can be exported to CSV and PDF formats directly from the system.</p>
            </details>
            <details class="rounded-xl border border-slate-200 bg-white p-4" data-aos="fade-up" data-aos-delay="160">
                <summary class="cursor-pointer font-semibold text-slate-900">How is pass rate calculated?</summary>
                <p class="mt-2 text-sm text-slate-600">Pass rate is derived from recorded fitness results marked with status "Pass".</p>
            </details>
        </div>
    </section>

    <section class="bg-gradient-to-r from-teal-700 to-sky-700 py-16 text-white">
        <div class="mx-auto flex w-full max-w-5xl flex-col items-center px-4 text-center sm:px-6 lg:px-8" data-aos="zoom-in">
            <h2 class="text-3xl font-extrabold">Ready to digitize your fitness monitoring workflow?</h2>
            <p class="mt-3 text-sm text-teal-50 sm:text-base">Start using JPJFit to manage participants, tests, and results with confidence.</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('login') }}" class="btn-secondary border-white/40 bg-white/10 text-white hover:bg-white/20">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary bg-white text-teal-800 hover:bg-slate-100">Register</a>
                @endif
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 py-8 text-slate-300">
        <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-3 px-4 text-sm sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} JPJFit - Fitness Monitoring System</p>
            <p>Built for Road Transport Department and Health Monitoring Operations</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
        });
    </script>
</body>
</html>
