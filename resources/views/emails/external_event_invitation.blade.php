@component('mail::message')
# Invitation: {{ $event->title }}

Hello,

You have been invited to the following event.

**Title:** {{ $event->title }}

**Description:** {{ $event->description }}

**Schedule:** {{ $event->start_date->format('M d, Y h:i A') }} to {{ $event->end_date->format('M d, Y h:i A') }}

**Sender:** {{ $event->creator->first_name }} {{ $event->creator->last_name }}

@component('mail::button', ['url' => $gcalLink, 'color' => 'success'])
Add to Google Calendar
@endcomponent

Best regards,<br>
SuriSync Government Operations
@endcomponent
