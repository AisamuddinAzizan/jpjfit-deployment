<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FitnessResultController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageContentController;
use App\Http\Controllers\MailSettingsController;
use App\Http\Controllers\NewsletterSubscriberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TestSessionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribe'])->name('newsletter.subscribe');

Route::post('/language', function (Request $request) {
    $validated = $request->validate([
        'locale' => ['required', 'in:en,ms'],
    ]);

    session(['locale' => $validated['locale']]);

    return back();
})->name('language.switch');

Route::middleware(['auth', 'audit'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('/notifications/read-all', function () {
        request()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Notifications marked as read.');
    })->name('notifications.read-all');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::get('/landing-page-content', [LandingPageContentController::class, 'edit'])->name('landing-content.edit');
        Route::put('/landing-page-content', [LandingPageContentController::class, 'update'])->name('landing-content.update');
        Route::post('/landing-page-content/hero-images', [LandingPageContentController::class, 'storeHeroImage'])->name('landing-content.hero-images.store');
        Route::delete('/landing-page-content/hero-images/{landingPageImage}', [LandingPageContentController::class, 'destroyHeroImage'])->name('landing-content.hero-images.destroy');
        Route::get('/mail-settings', [MailSettingsController::class, 'edit'])->name('mail-settings.edit');
        Route::put('/mail-settings', [MailSettingsController::class, 'update'])->name('mail-settings.update');
        Route::get('/newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])->name('newsletter-subscribers.index');
        Route::post('/newsletter-subscribers/send-email', [NewsletterSubscriberController::class, 'sendEmail'])->name('newsletter-subscribers.send-email');
        Route::get('/newsletter/test-session/{session}/participants', [
        NewsletterSubscriberController::class,
        'getParticipants'
        ])->name('newsletter.test-session.participants');
    });

    Route::middleware('role:admin|jpj_officer')->group(function () {
        Route::resource('participants', ParticipantController::class);
        Route::resource('test-sessions', TestSessionController::class);
        Route::resource('fitness-results', FitnessResultController::class);
    });

    Route::middleware('role:admin|health_officer')->group(function () {
        Route::resource('health-records', HealthRecordController::class);
    });

    Route::middleware('role:admin|jpj_officer|health_officer')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
        Route::post('/certificates/{certificate}/send-email', [CertificateController::class, 'sendEmail'])->name('certificates.send-email');
        Route::post('/certificates/send-pending-emails', [CertificateController::class, 'sendPendingEmails'])->name('certificates.send-pending-emails');
        Route::get('/certificates/{certificate}/preview', [CertificateController::class, 'preview'])->name('certificates.preview');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';