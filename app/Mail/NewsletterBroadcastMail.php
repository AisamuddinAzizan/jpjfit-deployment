<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class NewsletterBroadcastMail extends Mailable
{
    use Queueable;

    public function __construct(
        private readonly string $subjectLine,
        private readonly string $messageBody,
        private readonly ?string $recipientName = null,
    ) {
    }

    public function build(): self
    {
        return $this->subject($this->subjectLine)
            ->view('emails.newsletter-broadcast', [
                'subjectLine' => $this->subjectLine,
                'messageBody' => $this->messageBody,
                'recipientName' => $this->recipientName,
                'appName' => (string) config('app.name', 'JPJFit'),
            ]);
    }
}
