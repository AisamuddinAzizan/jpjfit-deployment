<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNewsletterBroadcastRequest;
use App\Mail\NewsletterBroadcastMail;
use App\Models\NewsletterSubscriber;
use App\Models\TestSession;
use App\Models\Participant;
use App\Models\FitnessResult;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        if (! Schema::hasTable('newsletter_subscribers')) {
            return view('newsletter-subscribers.index', [
                'subscribers' => collect(),
                'search' => $search,
            ]);
        }

        $subscribers = NewsletterSubscriber::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('subscribed_at')
            ->latest('id')
            ->get();

            $sessions = TestSession::all();

        return view('newsletter-subscribers.index', [
            'subscribers' => $subscribers,
            'search' => $search,
            'sessions' => $sessions,
        ]);
    }

    public function getParticipants(TestSession $session)
{
    $participantIds = FitnessResult::where(
        'test_session_id',
        $session->id
    )
    ->distinct()
    ->pluck('participant_id');

    $participants = Participant::whereIn('id', $participantIds)
        ->get()
        ->map(function ($participant) use ($session) {

            return [
                'id' => $participant->id,
                'name' => $participant->full_name,
                'email' => strtolower(trim($participant->email)),
                'session' => $session->session_code,
            ];
        })
        ->values();

    return response()->json($participants);
}

    public function sendEmail(SendNewsletterBroadcastRequest $request): RedirectResponse
    {
        set_time_limit(300);
        if (! Schema::hasTable('newsletter_subscribers')) {
            return back()->with('error', 'Newsletter subscriber table is unavailable.')->withInput();
        }

        $validated = $request->validated();
        $recipientMode = $validated['recipient_mode'];

        $recipientQuery = NewsletterSubscriber::query();

        if ($recipientMode === 'selected') {
            $recipientQuery->whereIn('id', $validated['subscriber_ids'] ?? []);
        }

        if (!empty($validated['test_session_id'])) {

        $participantIds = FitnessResult::where('test_session_id', $validated['test_session_id'])
            ->distinct()
            ->pluck('participant_id');

        Log::info('Raw participant emails', [
            'participants' => Participant::whereIn('id', $participantIds)
                ->pluck('email')
                ->toArray(),
        ]);

        $recipients = Participant::whereIn('id', $participantIds)
            ->get()
            ->map(function ($participant) {

                return [
                    'id' => $participant->id,
                    'name' => $participant->full_name,
                    'email' => strtolower(trim($participant->email)),
                ];     
            })
            ->filter(function ($participant) {

            if (empty($participant['email'])) {
                return false;
            }

            $email = strtolower(trim($participant['email']));

            if (
                str_ends_with($email, '@example.com') ||
                str_ends_with($email, '@example.org') ||
                str_ends_with($email, '@example.net')
            ) {
                return false;
            }

            return filter_var($participant['email'], FILTER_VALIDATE_EMAIL);
        })
            ->unique('email')
            ->values();

         } else {

        $recipients = $recipientQuery
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->map(function ($subscriber) {
                return [
                    'id' => $subscriber->id,
                    'name' => $subscriber->name,
                    'email' => strtolower(trim($subscriber->email)),
                ];
            })
            ->filter(function ($subscriber) {

                $email = $subscriber['email'];


                if (
                    str_ends_with($email, '@example.com') ||
                    str_ends_with($email, '@example.org') ||
                    str_ends_with($email, '@example.net')
                ) {
                    return false;
                }

                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })
             ->unique('email')
             ->values();
            
          }
            dd('TEST CONTROLLER');
          Log::info('Recipients', [
                'recipients' => $recipients->pluck('email')->toArray(),
            ]);

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No subscribers matched your recipient selection.')->withInput();
        }

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient['email'])->send(
                    new NewsletterBroadcastMail(
                        (string) $validated['subject'],
                        (string) $validated['message'],
                        $recipient['name'],
                    )
                );
                $sent++;
            } catch (Throwable $exception) {
                $failed++;
                Log::error('Failed to send newsletter broadcast email.', [
                    'subscriber_id' => $recipient['id'],
                    'subscriber_email' => $recipient['email'],
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($sent === 0) {
            return back()->with('Error!', 'No email was sent. Please review mail settings and try again.')->withInput();
        }

        $message = "Newsletter dispatch completed. Sent: {$sent}, Failed: {$failed}.";
        if (config('mail.default') === 'log') {
            $message .= ' Email content was written to laravel.log because MAIL_MAILER is set to log.';
        }

        return back()->with('success', $message);
    }
 }

