<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CertificateAvailableNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Certificate $certificate)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'JPJFit Certificate Available';
        $participantName = $notifiable->full_name ?? $notifiable->name ?? 'Participant';
        $sessionCode = $this->certificate->testSession?->session_code ?? '-';
        $issuedAt = $this->certificate->issued_at?->format('d M Y H:i') ?? now()->format('d M Y H:i');

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->view('emails.certificate-available', [
                'subject' => $subject,
                'participantName' => $participantName,
                'certificateNo' => $this->certificate->certificate_no,
                'sessionCode' => $sessionCode,
                'issuedAt' => $issuedAt,
                'appName' => (string) config('app.name', 'JPJFit'),
            ]);

        if ($this->certificate->pdf_path && Storage::disk('public')->exists($this->certificate->pdf_path)) {
            $mailMessage->attach(
                Storage::disk('public')->path($this->certificate->pdf_path),
                [
                    'as' => $this->certificate->certificate_no.'.pdf',
                    'mime' => 'application/pdf',
                ],
            );
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Certificate Ready',
            'certificate_id' => $this->certificate->id,
            'certificate_no' => $this->certificate->certificate_no,
            'message' => 'Certificate '.$this->certificate->certificate_no.' is now available.',
        ];
    }
}
