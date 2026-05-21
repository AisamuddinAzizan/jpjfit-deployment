<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $landingContent['meta_title'] ?? 'JPJFit - Fitness Monitoring System' }}</title>
    <meta name="description" content="{{ $landingContent['meta_description'] ?? 'JPJFit helps JPJ and KKM teams monitor fitness performance, sessions, and certifications in one modern platform.' }}">
    <link rel="icon" type="image/png" href="{{ asset('images/jpjfit_logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-body bg-slate-950 text-slate-100 antialiased" data-page="landing">
    <div id="scrollProgress" class="scroll-progress" aria-hidden="true"></div>

    <div id="pageLoader" class="page-loader" role="status" aria-live="polite" aria-label="Loading page">
        <span class="loader-dot"></span>
        <span class="loader-dot"></span>
        <span class="loader-dot"></span>
    </div>

    <canvas id="heroMouseTrail" class="hero-mouse-trail" aria-hidden="true"></canvas>

    <header id="landingNav" class="landing-nav fixed inset-x-0 top-0 z-50 transition duration-300">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <a href="#hero" class="flex items-center gap-3" aria-label="Go to hero section">
                <img src="{{ asset('images/jpjfit_logo.png') }}" alt="JPJFit logo" class="h-11 w-auto object-contain">
                <div>
                    <p class="font-heading text-lg font-extrabold text-white">{{ $landingContent['brand_name'] ?? 'JPJFit' }}</p>
                    <p class="text-xs uppercase tracking-[0.18em] text-sky-200">{{ $landingContent['brand_subtitle'] ?? 'Fitness Monitoring System' }}</p>
                </div>
            </a>

            <button id="mobileMenuToggle" type="button" class="landing-icon-btn md:hidden" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="mobileMenu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-100 md:flex" aria-label="Primary navigation">
                <a class="landing-link" href="#overview">{{ $landingContent['nav_overview_label'] ?? 'Overview' }}</a>
                <a class="landing-link" href="#features">{{ $landingContent['nav_features_label'] ?? 'Features' }}</a>
                <a class="landing-link" href="#workflow">{{ $landingContent['nav_workflow_label'] ?? 'How It Works' }}</a>
                <a class="landing-link" href="#faq">{{ $landingContent['nav_faq_label'] ?? 'FAQ' }}</a>
                <form method="POST" action="{{ route('language.switch') }}">
                    @csrf
                    <select name="locale" class="navbar-select" onchange="this.form.submit()" aria-label="Select language">
                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                        <option value="ms" @selected(app()->getLocale() === 'ms')>{{ __('Malay') }}</option>
                    </select>
                </form>
                <button id="themeToggleLanding" type="button" class="landing-icon-btn" aria-label="Toggle dark or light mode">
                    <span data-theme-label>Dark</span>
                </button>
                <a href="{{ route('login') }}" class="btn-secondary">{{ $landingContent['nav_login_button'] ?? 'Login' }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary cta-magnetic" data-text-scramble="{{ $landingContent['nav_register_button'] ?? 'Register' }}">{{ $landingContent['nav_register_button'] ?? 'Register' }}</a>
                @endif
            </nav>
        </div>

        <div id="mobileMenu" class="hidden border-t border-white/10 bg-slate-900/95 px-4 py-4 backdrop-blur md:hidden">
            <nav class="flex flex-col gap-3 text-sm" aria-label="Mobile navigation">
                <a class="landing-link" href="#overview">{{ $landingContent['nav_overview_label'] ?? 'Overview' }}</a>
                <a class="landing-link" href="#features">{{ $landingContent['nav_features_label'] ?? 'Features' }}</a>
                <a class="landing-link" href="#workflow">{{ $landingContent['nav_workflow_label'] ?? 'How It Works' }}</a>
                <a class="landing-link" href="#faq">{{ $landingContent['nav_faq_label'] ?? 'FAQ' }}</a>
                <form method="POST" action="{{ route('language.switch') }}">
                    @csrf
                    <select name="locale" class="navbar-select w-full" onchange="this.form.submit()" aria-label="Select language">
                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                        <option value="ms" @selected(app()->getLocale() === 'ms')>{{ __('Malay') }}</option>
                    </select>
                </form>
                <div class="mt-2 flex items-center gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary w-full justify-center">{{ $landingContent['nav_login_button'] ?? 'Login' }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary w-full justify-center">{{ $landingContent['nav_register_button'] ?? 'Register' }}</a>
                    @endif
                </div>
            </nav>
        </div>
    </header>

    @php
        $featureItems = [
            ['icon' => 'M5 13l4 4L19 7', 'title' => $landingContent['feature_1_title'] ?? 'Participant Management', 'text' => $landingContent['feature_1_text'] ?? 'Capture full participant details and status in one place.'],
            ['icon' => 'M8 7V3m8 4V3m-9 8h10m2 10H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z', 'title' => $landingContent['feature_2_title'] ?? 'Session Scheduling', 'text' => $landingContent['feature_2_text'] ?? 'Plan sessions and monitor attendance efficiently.'],
            ['icon' => 'M5 12h4l2-5 4 10 2-5h2', 'title' => $landingContent['feature_3_title'] ?? 'Auto Scoring', 'text' => $landingContent['feature_3_text'] ?? 'Automatic classification and pass/fail outcomes.'],
            ['icon' => 'M9 12l2 2 4-4m5 7l-8 4-8-4V5l8-4 8 4v12z', 'title' => $landingContent['feature_4_title'] ?? 'Reports & Certificates', 'text' => $landingContent['feature_4_text'] ?? 'Export and generate outputs within minutes.'],
        ];

        $workflowSteps = [
            ['title' => $landingContent['workflow_1_title'] ?? 'Register Participant', 'text' => $landingContent['workflow_1_text'] ?? 'Capture official profile and assign to a session.'],
            ['title' => $landingContent['workflow_2_title'] ?? 'Conduct Tests', 'text' => $landingContent['workflow_2_text'] ?? 'Record health screening and fitness metrics.'],
            ['title' => $landingContent['workflow_3_title'] ?? 'Auto Evaluate', 'text' => $landingContent['workflow_3_text'] ?? 'System calculates score and result status.'],
            ['title' => $landingContent['workflow_4_title'] ?? 'Generate Outputs', 'text' => $landingContent['workflow_4_text'] ?? 'Issue reports and certificates quickly.'],
        ];
    @endphp

    <main class="overflow-hidden">
        <section id="hero" class="hero-section relative w-full">
            <div id="heroParticles" class="hero-particles" aria-hidden="true"></div>

            <div class="swiper hero-swiper" aria-label="Hero slider">
                <div class="swiper-wrapper">
                    @foreach($heroSliderImages as $slide)
                        <article class="swiper-slide hero-slide" data-bg="{{ $slide['url'] }}">
                            <div class="hero-overlay"></div>
                        </article>
                    @endforeach
                </div>

                <div class="hero-swiper-nav">
                    <button class="hero-arrow hero-prev" type="button" aria-label="Previous slide">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button class="hero-arrow hero-next" type="button" aria-label="Next slide">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
                <div class="hero-pagination" aria-hidden="true"></div>
            </div>

            <div class="hero-content-layer">
                <div class="hero-content" data-aos="fade-up">
                    <p class="hero-chip">{{ $landingContent['hero_chip'] ?? 'Operational Readiness' }}</p>
                    <h1 class="font-heading text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">{{ $landingContent['hero_title'] ?? 'JPJFit - Fitness Monitoring System' }}</h1>
                    <p class="mt-4 max-w-2xl text-base text-slate-200 sm:text-lg">{{ $landingContent['hero_description'] ?? 'Track every participant journey from registration, session attendance, health screening, and result certification in one digital flow.' }}</p>
                    <div class="hero-actions mt-7 flex flex-wrap items-center justify-start gap-3">
                        <a href="{{ route('login') }}" class="btn-primary pulse-soft cta-magnetic" data-text-scramble="{{ $landingContent['hero_primary_button'] ?? 'Start Monitoring' }}">{{ $landingContent['hero_primary_button'] ?? 'Start Monitoring' }}</a>
                        <a href="#features" class="btn-secondary border-white/30 bg-white/10 text-white hover:bg-white/20">{{ $landingContent['hero_secondary_button'] ?? 'Explore Features' }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-secondary border-white/30 bg-white/10 text-white hover:bg-white/20">{{ $landingContent['hero_register_button'] ?? 'Create Account' }}</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 z-20 -translate-x-1/2 text-center">
                <p id="heroTypedText" class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-300"></p>
            </div>
        </section>

        <div id="overview-features-bg" class="section-rotator-bg">
            <section id="overview" class=" py-20">
                <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div data-aos="fade-right">
                        <p class="section-overline">{{ $landingContent['overview_overline'] ?? 'System Overview' }}</p>
                        <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['overview_title'] ?? 'A Command Center For Fitness Monitoring' }}</h2>
                        <p class="mt-4 text-slate-300">{{ $landingContent['overview_description'] ?? 'JPJFit is purpose-built for operational readiness. Teams can manage participants, test sessions, health checks, and outcomes from one secure, searchable platform.' }}</p>
                        <div class="mt-8 grid gap-3 sm:grid-cols-2">
                            <article class="overview-chip">
                                <span>{{ $landingContent['overview_chip_one_title'] ?? 'Role-Based Access' }}</span>
                                <p>{{ $landingContent['overview_chip_one_text'] ?? 'Admin, JPJ, and KKM workflows with secure separation.' }}</p>
                            </article>
                            <article class="overview-chip">
                                <span>{{ $landingContent['overview_chip_two_title'] ?? 'Audit-Ready Records' }}</span>
                                <p>{{ $landingContent['overview_chip_two_text'] ?? 'Consistent records for every test and certification event.' }}</p>
                            </article>
                        </div>
                    </div>

                    <div data-aos="fade-left" class="grid gap-4 sm:grid-cols-2">
                        <article class="overview-kpi">
                            <p class="text-xs uppercase tracking-[0.18em] text-sky-300">{{ $landingContent['overview_kpi_participants'] ?? 'Participants' }}</p>
                            <p class="mt-2 text-4xl font-extrabold text-white" data-counter="{{ $stats['total_participants'] }}" data-stat-key="total_participants">0</p>
                        </article>
                        <article class="overview-kpi">
                            <p class="text-xs uppercase tracking-[0.18em] text-emerald-300">{{ $landingContent['overview_kpi_sessions'] ?? 'Sessions' }}</p>
                            <p class="mt-2 text-4xl font-extrabold text-white" data-counter="{{ $stats['total_test_sessions'] }}" data-stat-key="total_test_sessions">0</p>
                        </article>
                        <article class="overview-kpi">
                            <p class="text-xs uppercase tracking-[0.18em] text-amber-300">{{ $landingContent['overview_kpi_pass_rate'] ?? 'Pass Rate' }}</p>
                            <p class="mt-2 text-4xl font-extrabold text-white"><span data-counter="{{ (int) round($stats['pass_rate']) }}" data-stat-key="pass_rate">0</span>%</p>
                        </article>
                        <article class="overview-kpi">
                            <p class="text-xs uppercase tracking-[0.18em] text-violet-300">{{ $landingContent['overview_kpi_live_workouts'] ?? 'Live Workouts' }}</p>
                            <p class="mt-2 text-4xl font-extrabold text-white" data-live-workout-counter="{{ $stats['live_workout_counter'] }}" data-stat-key="live_workout_counter">0</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="features" class="py-20">
                <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="text-center" data-aos="fade-up">
                        <p class="section-overline">{{ $landingContent['features_overline'] ?? 'Key Features' }}</p>
                        <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['features_title'] ?? 'Built To Move Fast And Stay Accurate' }}</h2>
                    </div>

                    <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($featureItems as $index => $feature)
                            <article class="feature-card tilt-card" data-aos="zoom-in" data-aos-delay="{{ 70 * ($index + 1) }}" tabindex="0">
                                <div class="feature-icon floating-icon" aria-hidden="true">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}" /></svg>
                                </div>
                                <h3 class="mt-6 font-heading text-lg font-bold text-white">{{ $feature['title'] }}</h3>
                                <p class="mt-2 text-sm text-slate-200">{{ $feature['text'] }}</p>
                                <span class="feature-glow" aria-hidden="true"></span>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        <section id="workflow" class="bg-slate-900 py-20">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center" data-aos="fade-up">
                    <p class="section-overline">{{ $landingContent['workflow_overline'] ?? 'How It Works' }}</p>
                    <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['workflow_title'] ?? 'Four Steps To Operational Clarity' }}</h2>
                </div>

                <div class="workflow-grid mt-12">
                    @foreach($workflowSteps as $index => $step)
                        <article class="workflow-step" data-step-progress data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                            <span class="workflow-number">{{ $index + 1 }}</span>
                            <h3 class="font-heading mt-4 text-lg font-bold text-white">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-300">{{ $step['text'] }}</p>
                            <div class="workflow-progress" aria-hidden="true"></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="preview" class="bg-slate-950 py-20">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="preview-card" data-aos="fade-right">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="font-heading text-2xl font-extrabold text-white">{{ $landingContent['preview_fitness_title'] ?? 'Fitness Test Preview' }}</h3>
                            <span class="fitness-badge">{{ $landingContent['preview_fitness_badge'] ?? 'UKJK Ready' }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-300">{{ $landingContent['preview_fitness_description'] ?? 'Interactive comparison slider showing baseline and improved readiness profile.' }}</p>

                        @php
                            $comparisonBeforeImage = asset('fitness/before.png');
                            $comparisonAfterImage = asset('fitness/after.png');
                            $comparisonBeforeVersion = @filemtime(public_path('fitness/before.png')) ?: 1;
                            $comparisonAfterVersion = @filemtime(public_path('fitness/after.png')) ?: 1;
                        @endphp

                        <div class="comparison-wrap mt-6" data-before-after data-position="50" style="min-height: 550px;">
                            <img
                                loading="lazy"
                                src="{{ $comparisonBeforeImage }}?v={{ $comparisonBeforeVersion }}"
                                alt="Before training"
                                class="comparison-img comparison-img-before-source"
                                onerror="this.onerror=null;this.src='{{ asset('images/fitness-before.svg') }}';"
                            >
                            <div class="comparison-after" data-after>
                                <img
                                    loading="lazy"
                                    src="{{ $comparisonAfterImage }}?v={{ $comparisonAfterVersion }}"
                                    alt="After training"
                                    class="comparison-img comparison-img-after-source"
                                    onerror="this.onerror=null;this.src='{{ asset('images/fitness-after.svg') }}';"
                                >
                            </div>

                            <div class="comparison-divider" data-divider>
                                <button
                                    type="button"
                                    class="comparison-handle"
                                    data-handle
                                    aria-label="Drag to compare before and after image"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="50"
                                >
                                    <span aria-hidden="true">||</span>
                                </button>
                            </div>

                            <input
                                type="range"
                                min="0"
                                max="100"
                                value="50"
                                class="comparison-range sr-only"
                                data-range
                                aria-label="Before and after comparison slider"
                            >

                            <span class="comparison-label comparison-label-before">Before</span>
                            <span class="comparison-label comparison-label-after">After</span>
                        </div>

                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            <div class="fitness-level level-low">{{ $landingContent['fitness_level_beginner'] ?? 'Beginner' }}</div>
                            <div class="fitness-level level-mid">{{ $landingContent['fitness_level_intermediate'] ?? 'Intermediate' }}</div>
                            <div class="fitness-level level-high">{{ $landingContent['fitness_level_advanced'] ?? 'Advanced' }}</div>
                        </div>
                    </article>

                    <article class="preview-card" data-aos="fade-left">
                        <h3 class="font-heading text-2xl font-extrabold text-white">{{ $landingContent['preview_countdown_title'] ?? 'Next Test Countdown' }}</h3>
                        <p class="mt-3 text-sm text-slate-300">{{ $landingContent['preview_countdown_description'] ?? 'Countdown updates live to the next scheduled session and supports quick BMI checks.' }}</p>
                        <div class="countdown-grid mt-6" data-countdown="{{ $nextTestCountdown }}" aria-label="Countdown timer">
                            <div><span data-countdown-days>00</span><small>Days</small></div>
                            <div><span data-countdown-hours>00</span><small>Hours</small></div>
                            <div><span data-countdown-minutes>00</span><small>Minutes</small></div>
                            <div><span data-countdown-seconds>00</span><small>Seconds</small></div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-slate-700 bg-slate-900/50 p-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Interactive Fitness Graph</p>
                            <canvas id="fitnessPreviewChart" class="mt-4 h-40 w-full" aria-label="Fitness graph preview"></canvas>
                        </div>

                        <button id="openBmiModal" type="button" class="btn-primary mt-6 w-full justify-center">{{ $landingContent['open_bmi_button'] ?? 'Open BMI Calculator' }}</button>
                    </article>
                </div>
            </div>
        </section>

        <section id="stats" class="stats-parallax relative py-20">
            <div class="stats-overlay"></div>
            <div class="mx-auto relative z-10 w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center" data-aos="fade-up">
                    <p class="section-overline text-slate-200">{{ $landingContent['stats_overline'] ?? 'Live Statistics' }}</p>
                    <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['stats_title'] ?? 'Measured Performance, Real Numbers' }}</h2>
                </div>

                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" id="statsCards">
                    <article class="stats-card skeleton-card" data-stat-card>
                        <div class="progress-ring" data-progress-ring="{{ min(100, (int) round($stats['pass_rate'])) }}" data-stat-key="pass_rate">
                            <svg viewBox="0 0 120 120" aria-hidden="true">
                                <circle cx="60" cy="60" r="52"></circle>
                                <circle cx="60" cy="60" r="52" class="progress"></circle>
                            </svg>
                            <span data-ring-value data-stat-key="pass_rate" data-stat-format="percent">{{ (int) round($stats['pass_rate']) }}%</span>
                        </div>
                        <h3>{{ $landingContent['stats_pass_rate_label'] ?? 'Pass Rate' }}</h3>
                    </article>
                    <article class="stats-card skeleton-card" data-stat-card>
                        <div class="progress-ring" data-progress-ring="{{ min(100, max(8, (int) round($stats['total_test_sessions']))) }}" data-stat-key="total_test_sessions">
                            <svg viewBox="0 0 120 120" aria-hidden="true">
                                <circle cx="60" cy="60" r="52"></circle>
                                <circle cx="60" cy="60" r="52" class="progress"></circle>
                            </svg>
                            <span data-ring-value data-counter="{{ $stats['total_test_sessions'] }}" data-stat-key="total_test_sessions">0</span>
                        </div>
                        <h3>{{ $landingContent['stats_total_sessions_label'] ?? 'Total Sessions' }}</h3>
                    </article>
                    <article class="stats-card skeleton-card" data-stat-card>
                        <div class="progress-ring" data-progress-ring="{{ min(100, max(10, (int) round($stats['total_participants'] / 2))) }}" data-stat-key="total_participants">
                            <svg viewBox="0 0 120 120" aria-hidden="true">
                                <circle cx="60" cy="60" r="52"></circle>
                                <circle cx="60" cy="60" r="52" class="progress"></circle>
                            </svg>
                            <span data-ring-value data-counter="{{ $stats['total_participants'] }}" data-stat-key="total_participants">0</span>
                        </div>
                        <h3>{{ $landingContent['stats_total_participants_label'] ?? 'Total Participants' }}</h3>
                    </article>
                    <article class="stats-card skeleton-card" data-stat-card>
                        <div class="progress-ring" data-progress-ring="{{ min(100, max(15, (int) round($stats['live_workout_counter'] / 2))) }}" data-stat-key="live_workout_counter">
                            <svg viewBox="0 0 120 120" aria-hidden="true">
                                <circle cx="60" cy="60" r="52"></circle>
                                <circle cx="60" cy="60" r="52" class="progress"></circle>
                            </svg>
                            <span data-ring-value data-live-workout-counter="{{ $stats['live_workout_counter'] }}" data-stat-key="live_workout_counter">0</span>
                        </div>
                        <h3>{{ $landingContent['stats_live_workout_label'] ?? 'Live Workout Counter' }}</h3>
                    </article>
                </div>
            </div>
        </section>

        <section id="dashboard-preview" class="bg-slate-900 py-20">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center" data-aos="fade-up">
                    <p class="section-overline">{{ $landingContent['dashboard_preview_overline'] ?? 'Dashboard Preview' }}</p>
                    <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['dashboard_preview_title'] ?? 'Interactive Monitoring Experience' }}</h2>
                </div>

                <div class="mt-12 grid gap-6 lg:grid-cols-3">
                    <article class="device-card rotate-device" data-aos="zoom-in">
                        <div class="device-frame">
                            <div class="mock-header"></div>
                            <div class="mock-body">
                                <div class="metric-glow"></div>
                                <div class="bar-chart">
                                    <span style="height: 35%"></span>
                                    <span style="height: 60%"></span>
                                    <span style="height: 74%"></span>
                                    <span style="height: 48%"></span>
                                    <span style="height: 86%"></span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="device-card floating-ui" data-aos="zoom-in" data-aos-delay="120">
                        <div class="device-frame mobile">
                            <div class="mock-header"></div>
                            <div class="mock-body">
                                <div class="ui-pill"></div>
                                <div class="ui-pill short"></div>
                                <div class="ui-grid">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="device-card" data-aos="zoom-in" data-aos-delay="220">
                        <div class="device-frame">
                            <div class="mock-header"></div>
                            <div class="mock-body">
                                <div class="metric-line"></div>
                                <div class="metric-line"></div>
                                <div class="metric-line"></div>
                                <div class="metric-line short"></div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="testimonials" class="bg-slate-950 py-20">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center" data-aos="fade-up">
                    <p class="section-overline">{{ $landingContent['testimonials_overline'] ?? 'Testimonials' }}</p>
                    <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['testimonials_title'] ?? 'Trusted By Operations Teams' }}</h2>
                </div>

                <div id="testimonialSkeletons" class="mt-10 grid gap-5 md:grid-cols-3">
                    <div class="skeleton-card h-40 rounded-2xl"></div>
                    <div class="skeleton-card h-40 rounded-2xl"></div>
                    <div class="skeleton-card h-40 rounded-2xl"></div>
                </div>

                <div class="swiper testimonial-swiper mt-10 hidden" id="testimonialSwiper" aria-label="Testimonials carousel">
                    <div class="swiper-wrapper" id="testimonialSlides">
                        @foreach($testimonials as $item)
                            <article class="swiper-slide testimonial-card">
                                <div class="flex items-center gap-3">
                                    <div class="avatar-wrap">{{ strtoupper(substr($item['name'], 0, 1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-white">{{ $item['name'] }}</p>
                                        <div class="stars" data-rating="{{ $item['rating'] }}" aria-label="{{ $item['rating'] }} star rating"></div>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-slate-300">{{ $item['content'] }}</p>
                                <span class="quote-icon" aria-hidden="true">"</span>
                            </article>
                        @endforeach
                    </div>
                    <div class="hero-pagination testimonial-pagination"></div>
                </div>
            </div>
        </section>

        <section
            id="faq"
            class="faq-bg-section py-20"
        >
            <div class="faq-bg-fixed-layer" aria-hidden="true"></div>
            <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="text-center" data-aos="fade-up">
                    <p class="section-overline">{{ $landingContent['faq_overline'] ?? 'FAQ' }}</p>
                    <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['faq_title'] ?? 'Questions You Might Ask' }}</h2>
                </div>

                <div class="mt-8">
                    <label for="faqSearch" class="sr-only">Search frequently asked questions</label>
                    <input id="faqSearch" type="search" placeholder="{{ $landingContent['faq_search_placeholder'] ?? 'Search FAQs...' }}" class="faq-search" aria-label="Search FAQ list">
                </div>

                <div class="mt-6 space-y-4" id="faqAccordion">
                    @foreach($faqs as $faq)
                        <article class="faq-item" data-faq-item>
                            <button type="button" class="faq-trigger" aria-expanded="false">
                                <span>{{ $faq['question'] }}</span>
                                <span class="faq-symbol" aria-hidden="true">+</span>
                            </button>
                            <div class="faq-panel" hidden>
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="cta" class="cta-section relative overflow-hidden py-20">
            <div class="mx-auto relative z-10 w-full max-w-5xl px-4 text-center sm:px-6 lg:px-8" data-aos="zoom-in">
                <p class="section-overline text-slate-100">{{ $landingContent['cta_overline'] ?? 'Start Your Digital Transformation' }}</p>
                <h2 class="font-heading mt-3 text-3xl font-extrabold text-white sm:text-4xl">{{ $landingContent['cta_title'] ?? 'Ready To Elevate Fitness Monitoring?' }}</h2>
                <p class="mx-auto mt-4 max-w-2xl text-slate-200">{{ $landingContent['cta_description'] ?? 'Launch faster reporting cycles, cleaner data collection, and stronger readiness with JPJFit.' }}</p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('login') }}" class="btn-secondary border-white/35 bg-white/10 text-white hover:bg-white/20 cta-magnetic" data-ripple>{{ $landingContent['cta_login_button'] ?? 'Login Now' }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary cta-magnetic" data-ripple data-text-scramble="{{ $landingContent['cta_register_button'] ?? 'Register Today' }}">{{ $landingContent['cta_register_button'] ?? 'Register Today' }}</a>
                    @endif
                </div>

                <div class="countdown-inline mt-8" data-offer-countdown="172800">
                    {{ $landingContent['cta_offer_prefix'] ?? 'Offer ends in:' }} <span data-offer-hours>00</span>h <span data-offer-minutes>00</span>m <span data-offer-seconds>00</span>s
                </div>
            </div>
        </section>
    </main>

    <footer class="landing-footer border-t border-slate-700/60 bg-slate-950 py-12">
        <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div>
                <p class="font-heading text-xl font-bold text-white">{{ $landingContent['brand_name'] ?? 'JPJFit' }}</p>
                <p class="mt-3 text-sm text-slate-300">{{ $landingContent['footer_brand_description'] ?? 'Energetic, modern and reliable fitness monitoring for public-sector operations.' }}</p>
                <div class="mt-5 flex items-center gap-3">
                    <a href="#" class="social-btn" aria-label="Visit Facebook">f</a>
                    <a href="#" class="social-btn" aria-label="Visit X">x</a>
                    <a href="#" class="social-btn" aria-label="Visit Instagram">i</a>
                </div>
            </div>

            <div>
                <h3 class="font-heading text-base font-bold text-white">{{ $landingContent['footer_quick_links_title'] ?? 'Quick Links' }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                    <li><a class="underline-anim" href="#overview">{{ $landingContent['nav_overview_label'] ?? 'Overview' }}</a></li>
                    <li><a class="underline-anim" href="#features">{{ $landingContent['nav_features_label'] ?? 'Features' }}</a></li>
                    <li><a class="underline-anim" href="#workflow">{{ $landingContent['nav_workflow_label'] ?? 'How It Works' }}</a></li>
                    <li><a class="underline-anim" href="#faq">{{ $landingContent['nav_faq_label'] ?? 'FAQ' }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-heading text-base font-bold text-white">{{ $landingContent['footer_support_title'] ?? 'Support' }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                    <li><a class="underline-anim" href="{{ route('login') }}">{{ $landingContent['nav_login_button'] ?? 'Login' }}</a></li>
                    @if (Route::has('register'))
                        <li><a class="underline-anim" href="{{ route('register') }}">{{ $landingContent['nav_register_button'] ?? 'Register' }}</a></li>
                    @endif
                    <li><a class="underline-anim" href="#cta">Contact Team</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-heading text-base font-bold text-white">{{ $landingContent['footer_newsletter_title'] ?? 'Newsletter' }}</h3>
                <p class="mt-3 text-sm text-slate-300">{{ $landingContent['footer_newsletter_description'] ?? 'Get release notes and monitoring updates.' }}</p>
                <form method="POST" action="{{ route('newsletter.subscribe') }}" id="newsletterForm" class="mt-4 space-y-3">
                    @csrf
                    <label for="newsletterName" class="sr-only">Name</label>
                    <input id="newsletterName" name="name" type="text" class="newsletter-input" placeholder="{{ $landingContent['footer_newsletter_name_placeholder'] ?? 'Your name' }}">
                    <label for="newsletterEmail" class="sr-only">Email</label>
                    <input id="newsletterEmail" name="email" type="email" class="newsletter-input" placeholder="{{ $landingContent['footer_newsletter_email_placeholder'] ?? 'you@example.com' }}" required>
                    <button type="submit" class="btn-primary w-full justify-center cta-magnetic" data-ripple>{{ $landingContent['footer_newsletter_button'] ?? 'Subscribe' }}</button>
                </form>
            </div>
        </div>

        <div class="mx-auto mt-10 flex w-full max-w-7xl flex-col items-center justify-between gap-3 border-t border-slate-800 pt-6 px-4 text-xs text-slate-400 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} {{ $landingContent['footer_copy_text'] ?? 'JPJFit - Fitness Monitoring System' }}</p>
            <p>{{ $landingContent['footer_bottom_note'] ?? 'Road Transport Department & Health Monitoring Operations' }}</p>
        </div>
    </footer>

    <div id="landingFab" class="landing-fab" aria-label="Quick actions">
        <button id="goTopBtn" type="button" class="fab-btn hidden" aria-label="Go to top">{{ $landingContent['fab_top_button'] ?? 'Top' }}</button>
        <a href="#cta" class="fab-btn" aria-label="Jump to call to action">{{ $landingContent['fab_cta_button'] ?? 'CTA' }}</a>
        <button id="highContrastToggle" type="button" class="fab-btn" aria-label="Toggle high contrast mode">{{ $landingContent['fab_hc_button'] ?? 'HC' }}</button>
    </div>

    <dialog id="bmiModal" class="bmi-modal" aria-label="BMI calculator dialog">
        <form method="dialog" class="bmi-card">
            <div class="flex items-center justify-between">
                <h3 class="font-heading text-xl font-bold text-white">{{ $landingContent['bmi_modal_title'] ?? 'BMI Calculator' }}</h3>
                <button type="submit" class="landing-icon-btn" aria-label="Close BMI calculator">X</button>
            </div>
            <div class="mt-5 grid gap-3">
                <label class="text-sm">{{ $landingContent['bmi_height_label'] ?? 'Height (cm)' }}
                    <input id="bmiHeight" type="number" min="100" max="220" value="170" class="newsletter-input mt-1">
                </label>
                <label class="text-sm">{{ $landingContent['bmi_weight_label'] ?? 'Weight (kg)' }}
                    <input id="bmiWeight" type="number" min="30" max="220" value="70" class="newsletter-input mt-1">
                </label>
            </div>
            <button id="calcBmiBtn" type="button" class="btn-primary mt-5 w-full justify-center">{{ $landingContent['bmi_calculate_button'] ?? 'Calculate' }}</button>
            <p id="bmiResult" class="mt-4 text-sm text-slate-200" aria-live="polite">{{ $landingContent['bmi_result_default'] ?? 'Enter values and calculate your BMI.' }}</p>
        </form>
    </dialog>
</body>
</html>
