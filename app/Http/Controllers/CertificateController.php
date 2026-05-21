<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\FitnessResult;
use App\Models\TestSession;
use App\Notifications\CertificateAvailableNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

    $sessionFilter = $request->query('session');
    $certificates = Certificate::with(['participant', 'testSession', 'issuer'])
    ->when($sessionFilter, function ($query) use ($sessionFilter) {
    $query->where('test_session_id', $sessionFilter);
})
    
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('certificate_no', 'like', "%{$search}%")
                        ->orWhereHas('participant', function ($participantQuery) use ($search) {
                            $participantQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('participant_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('testSession', function ($sessionQuery) use ($search) {
                            $sessionQuery->where('session_code', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('issued_at')
            ->get();

        $issuedPairs = Certificate::select('participant_id', 'test_session_id')
            ->get()
            ->mapWithKeys(fn ($certificate) => [$certificate->participant_id.'-'.$certificate->test_session_id => true]);

        $eligiblePairs = FitnessResult::query()
             ->whereRaw('LOWER(result_status) = ?', ['pass'])
            ->get(['participant_id', 'test_session_id'])
            ->reject(fn ($result) => $issuedPairs->has($result->participant_id.'-'.$result->test_session_id))
            ->values();

        $eligibleSessionCounts = $eligiblePairs
            ->groupBy('test_session_id')
            ->map(fn ($results) => $results->count());

       $sessions = TestSession::query()
            ->orderByDesc('session_date')
            ->get()
            ->keyBy('id');

      $eligibleSessions = $sessions->map(function ($session) use ($eligibleSessionCounts) {
            return [
                'session' => $session,
                'pending_count' => $eligibleSessionCounts[$session->id] ?? 0,
        ];
    })->values();

        $pendingEmailCount = Certificate::with('participant:id,email')
            ->whereNull('emailed_at')
            ->get()
            ->filter(fn (Certificate $certificate): bool =>
                ! empty($certificate->participant?->email)
                && filter_var($certificate->participant?->email, FILTER_VALIDATE_EMAIL)
                && ! empty($certificate->pdf_path)
                && Storage::disk('public')->exists($certificate->pdf_path)
            )
            ->count();

        return view('certificates.index', compact('certificates', 'eligibleSessions', 'search', 'pendingEmailCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        set_time_limit(300);
        $validated = $request->validate([
            'test_session_id' => ['required', 'integer', 'exists:test_sessions,id'],
        ]);

        $session = TestSession::findOrFail($validated['test_session_id']);

        $passResults = FitnessResult::with(['participant', 'testSession'])
            ->where('test_session_id', $session->id)
             ->whereRaw('LOWER(result_status) = ?', ['pass'])
            ->latest('id')
            ->get()
            ->unique('participant_id')
            ->values();

        if ($passResults->isEmpty()) {
            return back()->with('error', 'No passing participants found for the selected session.');
        }

        $issuedParticipantIds = Certificate::query()
            ->where('test_session_id', $session->id)
            ->pluck('participant_id')
            ->flip();

        $resultsToGenerate = $passResults
            ->reject(fn (FitnessResult $result) => $issuedParticipantIds->has($result->participant_id))
            ->values();

        if ($resultsToGenerate->isEmpty()) {
            return back()->with('success', 'All eligible participants in this session already have certificates.');
        }

        $usingLogMailer = config('mail.default') === 'log';
        $generated = 0;
        $emailed = 0;
        $emailFailed = 0;
        $skippedExisting = 0;

        foreach ($resultsToGenerate as $fitnessResult) {
            $participant = $fitnessResult->participant;
            if (! $participant) {
                continue;
            }

            $certificate = Certificate::query()->where([
                'participant_id' => $participant->id,
                'test_session_id' => $session->id,
            ])->first();

            if ($certificate && ! empty($certificate->pdf_path) && Storage::disk('public')->exists($certificate->pdf_path)) {
                $skippedExisting++;
                continue;
            }

            if (! $certificate) {
                $certificate = new Certificate([
                    'participant_id' => $participant->id,
                    'test_session_id' => $session->id,
                    'certificate_no' => $this->generateCertificateNumber(),
                    'issued_at' => now(),
                    'issued_by' => auth()->id(),
                ]);

                try {
                    $certificate->save();
                } catch (UniqueConstraintViolationException $exception) {
                    $certificate = Certificate::query()->where([
                        'participant_id' => $participant->id,
                        'test_session_id' => $session->id,
                    ])->first();

                    if (! $certificate) {
                        throw $exception;
                    }

                    if (! empty($certificate->pdf_path) && Storage::disk('public')->exists($certificate->pdf_path)) {
                        $skippedExisting++;
                        continue;
                    }
                }
            }

            $pdf = Pdf::loadView('certificates.pdf', [
                'certificate' => $certificate,
                'participant' => $participant,
                'session' => $session,
                'fitnessResult' => $fitnessResult,
            ])->setPaper('a4', 'landscape');

            $fileName = 'certificates/'.$certificate->certificate_no.'.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());

            $certificate->issued_at = now();
            $certificate->issued_by = auth()->id();
            $certificate->pdf_path = $fileName;
            $certificate->save();

            $generated++;
        }

        $successMessage = "Generated {$generated} certificate(s) for session {$session->session_code}.";
        if ($skippedExisting > 0) {
            $successMessage .= " Skipped existing: {$skippedExisting}.";
        }
        if ($usingLogMailer) {
            $successMessage .= ' Email notifications were generated to log only (MAIL_MAILER=log).';
        } else {
            $successMessage .= " Emails sent: {$emailed}.";
            if ($emailFailed > 0) {
                $successMessage .= " Failed emails: {$emailFailed}.";
            }
        }

        return redirect()->route('certificates.index')->with('success', $successMessage);
    }

    private function generateCertificateNumber(): string
    {
        do {
            $certificateNo = 'JPJFIT-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Certificate::query()->where('certificate_no', $certificateNo)->exists());

        return $certificateNo;
    }

    public function download(Certificate $certificate)
    {
        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            abort(404, 'Certificate file not found.');
        }

        return Storage::disk('public')->download($certificate->pdf_path, $certificate->certificate_no.'.pdf');
    }

    public function preview(Certificate $certificate): BinaryFileResponse
    {
        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            abort(404, 'Certificate file not found.');
        }

        $absolutePath = Storage::disk('public')->path($certificate->pdf_path);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$certificate->certificate_no.'.pdf"',
        ]);
    }

    public function sendEmail(Certificate $certificate): RedirectResponse
    {
        if (config('mail.default') === 'log') {
            return back()->with('error', 'MAIL_MAILER is set to log. Switch to Gmail/SMTP in Mail Settings to send real participant emails.');
        }

        $participant = $certificate->participant;

        if (! $participant || empty($participant->email)) {
            return back()->with('error', 'Participant email is missing. Unable to send certificate email.');
        }

        if (! filter_var($participant->email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Participant email is invalid. Please update participant email before sending certificate.');
        }

        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            return back()->with('error', 'Certificate PDF file not found. Generate the certificate again before sending email.');
        }

        try {
            $participant->notify(new CertificateAvailableNotification($certificate));
            $certificate->forceFill(['emailed_at' => now()])->save();
        } catch (Throwable $exception) {
            Log::error('Failed to send certificate email.', [
                'certificate_id' => $certificate->id,
                'participant_id' => $participant->id,
                'participant_email' => $participant->email,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Failed to send email. Please check mail settings and try again.');
        }

        return back()->with('success', 'Certificate email sent to '.$participant->email.'.');
    }

    public function sendPendingEmails(): RedirectResponse
    {
        if (config('mail.default') === 'log') {
            return back()->with('error', 'MAIL_MAILER is set to log. Switch to Gmail/SMTP in Mail Settings before using Send All Pending.');
        }

        $pendingCertificates = Certificate::with('participant')
            ->whereNull('emailed_at')
            ->latest('issued_at')
            ->get();

        if ($pendingCertificates->isEmpty()) {
            return back()->with('success', 'No pending certificate emails to send.');
        }

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($pendingCertificates as $certificate) {
            $participant = $certificate->participant;

            if (! $participant || empty($participant->email) || ! filter_var($participant->email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
                $skipped++;
                continue;
            }

            try {
                $participant->notify(new CertificateAvailableNotification($certificate));
                $certificate->forceFill(['emailed_at' => now()])->save();
                $sent++;
            } catch (Throwable $exception) {
                $failed++;

                Log::error('Failed to send pending certificate email.', [
                    'certificate_id' => $certificate->id,
                    'participant_id' => $participant->id,
                    'participant_email' => $participant->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return back()->with('success', "Pending certificate email run completed. Sent: {$sent}, Skipped: {$skipped}, Failed: {$failed}.");
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        if ($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path)) {
            Storage::disk('public')->delete($certificate->pdf_path);
        }

        $certificate->delete();

        return redirect()->route('certificates.index')->with('success', 'Certificate deleted successfully.');
    }
}
