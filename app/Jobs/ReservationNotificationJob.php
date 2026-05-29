<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReservationNotificationJob implements ShouldQueue
{
    use Queueable;

    protected string $reservationId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reservation = \App\Models\Reservation::with('requester')->find($this->reservationId);
        if ($reservation && $reservation->requester) {
            $reservation->requester->notify(new \App\Notifications\ReservationStatusNotification($reservation));
        }
    }
}
