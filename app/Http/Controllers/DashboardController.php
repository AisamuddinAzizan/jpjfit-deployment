<?php

namespace App\Http\Controllers;

use App\Models\FitnessResult;
use App\Models\NewsletterSubscriber;
use App\Models\Participant;
use App\Models\TestSession;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        $participantsCount = Participant::count();
        $upcomingTestsCount = TestSession::whereDate('session_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->count();

        $passCount = FitnessResult::where('result_status', 'Pass')->count();
        $failCount = FitnessResult::where('result_status', 'Fail')->count();

        $classificationBreakdown = FitnessResult::select('classification', DB::raw('COUNT(*) as total'))
            ->groupBy('classification')
            ->pluck('total', 'classification');

        $startYear = now()->year - 9;

        $sessionOutcomeSeries = TestSession::query()
            ->whereBetween('session_date', [
                now()->copy()->setYear($startYear)->startOfYear()->toDateString(),
                now()->copy()->endOfYear()->toDateString(),
            ])
            ->withCount([
                'fitnessResults as pass_total' => fn ($query) => $query->where('result_status', 'Pass'),
                'fitnessResults as fail_total' => fn ($query) => $query->where('result_status', 'Fail'),
            ])
            ->orderBy('session_date')
            ->get()
            ->map(fn (TestSession $session) => [
                'label' => $session->session_date?->translatedFormat('d M Y') ?? '-',
                'pass' => (int) $session->pass_total,
                'fail' => (int) $session->fail_total,
            ]);

        $recentSessions = TestSession::withCount('participants')
            ->latest('session_date')
            ->take(5)
            ->get();

        $notifications = $user->notifications()->latest()->take(8)->get();

        $newsletterSubscribers = collect();
        if ($user->hasRole('admin')) {
            $newsletterSubscribers = NewsletterSubscriber::query()
                ->latest('subscribed_at')
                ->latest('id')
                ->get();
        }

        return view('dashboard', [
            'stats' => [
                'participants' => $participantsCount,
                'upcoming_tests' => $upcomingTestsCount,
                'pass_count' => $passCount,
                'fail_count' => $failCount,
            ],
            'classificationBreakdown' => $classificationBreakdown,
            'sessionOutcomeSeries' => $sessionOutcomeSeries,
            'recentSessions' => $recentSessions,
            'notifications' => $notifications,
            'newsletterSubscribers' => $newsletterSubscribers,
        ]);
    }
}
