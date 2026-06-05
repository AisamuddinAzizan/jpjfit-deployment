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
                'participants' => Participant::whereIn('id', $participantIds)->pluck('email')->toArray(),
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
            
        Log::info('Recipients', [
            'recipients' => $recipients->pluck('email')->toArray(),
        ]);

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No subscribers matched your recipient selection.')->withInput();
        }

        $sent = 0;
        $failed = 0;

        // --- MULA BAHAGIAN DEBUG ---
        foreach ($recipients as $recipient) {
            try {
                // KITA TUKAR ->queue() KEPADA ->send() UNTUK DEBUG REAL-TIME RUGI/UNTUNG
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

                // INI AKAN FORCE PRINT RALAT SMTP TERUS KE SKRIN BROWSER ANDA!
                dd([
                    'MESEJ RALAT UTAMA' => $exception->getMessage(),
                    'EMEL PENERIMA' => $recipient['email'],
                    'FAIL' => $exception->getFile() . ' Line: ' . $exception->getLine(),
                    'SILA SEMAK' => 'Jika keluar "Authentication accepted", bermakna password betul. Jika "Connection refused", bermakna hos salah.'
                ]);
            }
        }
        // --- TAMAT BAHAGIAN DEBUG ---

        if ($sent === 0) {
            return back()->with('Error!', 'No email was sent. Please review mail settings and try again.')->withInput();
        }

        $message = "Newsletter dispatch completed. Sent: {$sent}, Failed: {$failed}.";
        return back()->with('success', $message);
    }