<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationNotification extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Invitation: ' . $this->event->title)
                    ->greeting('Hello ' . $notifiable->first_name . ',')
                    ->line('You have been invited to the following event.')
                    ->line('**Title:** ' . $this->event->title)
                    ->line('**Description:** ' . $this->event->description)
                    ->line('**Schedule:** ' . $this->event->start_date->format('M d, Y h:i A') . ' to ' . $this->event->end_date->format('M d, Y h:i A'))
                    ->action('View Event', url('/events/' . $this->event->id))
                    ->line('Sender: ' . $this->event->creator->first_name . ' ' . $this->event->creator->last_name)
                    ->salutation("Best regards,\nSuriSync Government Operations");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'message' => 'You have been invited to ' . $this->event->title,
        ];
    }
}
