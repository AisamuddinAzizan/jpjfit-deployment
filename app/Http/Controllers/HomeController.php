<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FitnessResult;
use App\Models\NewsletterSubscriber;
use App\Models\Participant;
use App\Models\Testimonial;
use App\Models\TestSession;
use App\Services\LandingPageContentService;
use App\Services\LandingPageImageService;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(
        LandingPageContentService $landingPageContentService,
        LandingPageImageService $landingPageImageService,
    ): View
    {
        $stats = $this->getStats();
        $landingContent = $landingPageContentService->all();

        return view('home.index', [
            'stats' => $stats,
            'testimonials' => $this->getTestimonials(),
            'faqs' => $this->getFaqs($landingContent),
            'nextTestCountdown' => $this->nextTestCountdown(),
            'landingContent' => $landingContent,
            'heroSliderImages' => $landingPageImageService->heroSlides(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => $this->getStats(),
            'cached_at' => now()->toIso8601String(),
        ]);
    }

    public function testimonials(): JsonResponse
    {
        return response()->json([
            'data' => $this->getTestimonials(),
        ]);
    }

    public function subscribe(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        if (! Schema::hasTable('newsletter_subscribers')) {
            $message = 'Newsletter subscription is temporarily unavailable.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 503)
                : back()->with('error', $message);
        }

        NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $validated['email']],
            ['name' => $validated['name'] ?? null, 'subscribed_at' => now()],
        );

        $message = 'Thanks for subscribing. You will receive fitness updates soon.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    private function getStats(): array
    {
        return $this->cache()->remember('home:stats', now()->addMinutes(5), function (): array {
            $totalParticipants = Schema::hasTable('participants') ? Participant::query()->count() : 0;
            $totalTestSessions = Schema::hasTable('test_sessions') ? TestSession::query()->count() : 0;

            $totalResults = Schema::hasTable('fitness_results') ? FitnessResult::query()->count() : 0;
            $passResults = Schema::hasTable('fitness_results')
                ? FitnessResult::query()->where('result_status', 'Pass')->count()
                : 0;
            $passRate = $totalResults > 0 ? round(($passResults / $totalResults) * 100, 1) : 0;

            return [
                'total_participants' => $totalParticipants,
                'total_test_sessions' => $totalTestSessions,
                'pass_rate' => $passRate,
                'live_workout_counter' => max(12, (int) round($totalTestSessions * 1.6)),
            ];
        });
    }

    private function getTestimonials(): Collection
    {
        return $this->cache()->remember('home:testimonials', now()->addMinutes(10), function (): Collection {
            if (! Schema::hasTable('testimonials')) {
                return collect([
                    [
                        'name' => 'Operations Lead, JPJ',
                        'avatar' => null,
                        'content' => 'JPJFit cut our manual reporting workload and improved visibility across test cycles.',
                        'rating' => 5,
                    ],
                    [
                        'name' => 'Health Officer, KKM',
                        'avatar' => null,
                        'content' => 'Health screening and fitness performance can now be tracked consistently in one platform.',
                        'rating' => 5,
                    ],
                ]);
            }

            return Testimonial::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(10)
                ->get(['id', 'name', 'avatar', 'content', 'rating']);
        });
    }

    private function getFaqs(array $landingContent): Collection
    {
        $faqsFromLandingContent = collect(range(1, 4))
            ->map(function (int $index) use ($landingContent): ?array {
                $question = trim((string) ($landingContent['faq_'.$index.'_question'] ?? ''));
                $answer = trim((string) ($landingContent['faq_'.$index.'_answer'] ?? ''));

                if ($question === '' || $answer === '') {
                    return null;
                }

                return [
                    'id' => $index,
                    'question' => $question,
                    'answer' => $answer,
                ];
            })
            ->filter()
            ->values();

        if ($faqsFromLandingContent->isNotEmpty()) {
            return $faqsFromLandingContent;
        }

        $locale = app()->getLocale();

        return $this->cache()->remember('home:faqs:'.$locale, now()->addMinutes(30), function () use ($locale): Collection {
            if (! Schema::hasTable('faqs')) {
                if ($locale === 'ms') {
                    return collect([
                        [
                            'question' => 'Siapa yang boleh mengakses JPJFit?',
                            'answer' => 'Pentadbir Sistem, Pegawai JPJ dan Pegawai Kesihatan boleh mengakses JPJFit berdasarkan peranan yang ditetapkan.',
                        ],
                        [
                            'question' => 'Bolehkah laporan dieksport?',
                            'answer' => 'Ya. Laporan boleh dieksport dalam format CSV dan PDF untuk perkongsian serta arkib.',
                        ],
                    ]);
                }

                return collect([
                    [
                        'question' => 'Who can access JPJFit?',
                        'answer' => 'System Admin, JPJ Officers and Health Officers with role-based access.',
                    ],
                    [
                        'question' => 'Can reports be exported?',
                        'answer' => 'Yes. Reports can be exported as CSV and PDF for operational use.',
                    ],
                ]);
            }

            $hasMalayColumns = Schema::hasColumns('faqs', ['question_ms', 'answer_ms']);

            if ($locale === 'ms' && $hasMalayColumns) {
                return Faq::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit(12)
                    ->get([
                        'id',
                        'sort_order',
                        'is_active',
                        'question',
                        'answer',
                        'question_ms',
                        'answer_ms',
                    ])
                    ->map(function (Faq $faq) {
                        return [
                            'id' => $faq->id,
                            'question' => $faq->question_ms ?: $faq->question,
                            'answer' => $faq->answer_ms ?: $faq->answer,
                        ];
                    });
            }

            return Faq::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get(['id', 'question', 'answer']);
        });
    }

    private function nextTestCountdown(): int
    {
        if (! Schema::hasTable('test_sessions')) {
            return 24 * 60 * 60;
        }

        $nextSession = TestSession::query()
            ->whereDate('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->first();

        if (! $nextSession) {
            return 8 * 60 * 60;
        }

        return max(0, now()->diffInSeconds($nextSession->session_date, false));
    }

    private function cache(): CacheRepository
    {
        return Cache::store(config('cache.default'));
    }
}
