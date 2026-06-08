<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementPublishedNotification extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    protected $announcement;

    public function __construct($announcement)
    {
        $this->announcement = $announcement;
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
                    ->subject('New Announcement: ' . $this->announcement->title)
                    ->greeting('Hello ' . $notifiable->first_name . ',')
                    ->line('A new announcement has been published.')
                    ->line('**Title:** ' . $this->announcement->title)
                    ->line('**Message:** ' . str($this->announcement->content)->limit(100))
                    ->action('Read Announcement', url('/announcements/' . $this->announcement->id))
                    ->salutation("Best regards,\nCITO Workspace Operations");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => 'New announcement: ' . $this->announcement->title,
        ];
    }
}
