<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusNotification extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
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
                    ->subject('Reservation Status Update: ' . ucfirst($this->reservation->status))
                    ->greeting('Hello ' . $notifiable->first_name . ',')
                    ->line('Your room reservation status has been updated to **' . strtoupper($this->reservation->status) . '**.')
                    ->line('**Room:** ' . $this->reservation->room_name)
                    ->line('**Schedule:** ' . $this->reservation->start_time->format('M d, Y h:i A') . ' to ' . $this->reservation->end_time->format('M d, Y h:i A'))
                    ->action('View Reservation', url('/reservations/' . $this->reservation->id))
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
            'reservation_id' => $this->reservation->id,
            'status' => $this->reservation->status,
            'message' => 'Your reservation for ' . $this->reservation->room_name . ' is now ' . $this->reservation->status,
        ];
    }
}
