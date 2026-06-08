<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExternalEventInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    /**
     * Create a new message instance.
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation: ' . $this->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $startDateStr = $this->event->start_date->setTimezone('UTC')->format('Ymd\THis\Z');
        $endDateStr = $this->event->end_date->setTimezone('UTC')->format('Ymd\THis\Z');
        
        $gcalLink = "https://calendar.google.com/calendar/render?action=TEMPLATE" . 
            "&text=" . urlencode($this->event->title) . 
            "&dates=" . $startDateStr . "/" . $endDateStr . 
            "&details=" . urlencode($this->event->description);

        return new Content(
            markdown: 'emails.external_event_invitation',
            with: [
                'gcalLink' => $gcalLink,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $icsContent = "BEGIN:VCALENDAR\r\n" .
            "VERSION:2.0\r\n" .
            "PRODID:-//CITO Workspace//EN\r\n" .
            "METHOD:REQUEST\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:event-{$this->event->id}@citoworkspace\r\n" .
            "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n" .
            "DTSTART:" . $this->event->start_date->setTimezone('UTC')->format('Ymd\THis\Z') . "\r\n" .
            "DTEND:" . $this->event->end_date->setTimezone('UTC')->format('Ymd\THis\Z') . "\r\n" .
            "SUMMARY:" . $this->event->title . "\r\n" .
            "DESCRIPTION:" . $this->event->description . "\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR";

        return [
            Attachment::fromData(fn () => $icsContent, 'invite.ics')
                ->withMime('text/calendar; charset=UTF-8; method=REQUEST'),
        ];
    }
}
