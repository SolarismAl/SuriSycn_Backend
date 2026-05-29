<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EventReminderJob implements ShouldQueue
{
    use Queueable;

    protected string $eventId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Fetch event, check time, and send reminder to tagged users and creator
        $event = \App\Models\Event::find($this->eventId);
        if ($event) {
            // Implementation of notification
        }
    }
}
