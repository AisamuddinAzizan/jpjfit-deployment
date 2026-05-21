<?php

namespace App\Notifications;

use App\Models\TestSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestScheduleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly TestSession $session)
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
        return (new MailMessage)
            ->subject('JPJFit UKJK Test Schedule')
            ->greeting('Assalamualaikum / Salam Sejahtera,')
            ->line('A new UKJK fitness test schedule has been assigned.')
            ->line('Session: '.$this->session->title)
            ->line('Code: '.$this->session->session_code)
            ->line('Date: '.$this->session->session_date?->format('d M Y'))
            ->line('Location: '.$this->session->location)
            ->action('View Session', url('/test-sessions/'.$this->session->id))
            ->line('Please be present on time and prepare accordingly.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New UKJK Test Schedule',
            'session_id' => $this->session->id,
            'session_code' => $this->session->session_code,
            'message' => 'Session '.$this->session->title.' is scheduled for '.$this->session->session_date?->format('d M Y').'.',
        ];
    }
}
