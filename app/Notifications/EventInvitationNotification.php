<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationNotification extends Notification
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
        $startDateStr = $this->event->start_date->setTimezone('UTC')->format('Ymd\THis\Z');
        $endDateStr = $this->event->end_date->setTimezone('UTC')->format('Ymd\THis\Z');
        
        $gcalLink = "https://calendar.google.com/calendar/render?action=TEMPLATE" . 
            "&text=" . urlencode($this->event->title) . 
            "&dates=" . $startDateStr . "/" . $endDateStr . 
            "&details=" . urlencode($this->event->description);

        return (new MailMessage)
                    ->subject('Invitation: ' . $this->event->title)
                    ->greeting('Hello ' . $notifiable->first_name . ',')
                    ->line('You have been invited to the following event.')
                    ->line('**Title:** ' . $this->event->title)
                    ->line('**Description:** ' . $this->event->description)
                    ->line('**Schedule:** ' . $this->event->start_date->format('M d, Y h:i A') . ' to ' . $this->event->end_date->format('M d, Y h:i A'))
                    ->action('Add to Google Calendar', $gcalLink)
                    ->line('Sender: ' . $this->event->creator->first_name . ' ' . $this->event->creator->last_name)
                    ->salutation("Best regards,\nSuriSync Government Operations")
                    ->attachData($this->buildIcsContent(), 'invite.ics', [
                        'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
                    ]);
    }

    protected function buildIcsContent(): string
    {
        return "BEGIN:VCALENDAR\r\n" .
            "VERSION:2.0\r\n" .
            "PRODID:-//SuriSync//EN\r\n" .
            "METHOD:REQUEST\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:event-{$this->event->id}@surisync\r\n" .
            "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n" .
            "DTSTART:" . $this->event->start_date->setTimezone('UTC')->format('Ymd\THis\Z') . "\r\n" .
            "DTEND:" . $this->event->end_date->setTimezone('UTC')->format('Ymd\THis\Z') . "\r\n" .
            "SUMMARY:" . $this->event->title . "\r\n" .
            "DESCRIPTION:" . $this->event->description . "\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR";
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
