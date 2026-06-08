@component('mail::message')


Dear Sir/Madam,

You are formally invited to attend the following {{ $event->is_meeting ? 'meeting' : 'event' }} organized by the City Information Technology Office.

@component('mail::panel')
**{{ $event->is_meeting ? 'Meeting' : 'Event' }}:** {{ $event->title }}

**Description:**<br>
{!! nl2br(e($event->description)) !!}

**Schedule:** {{ $event->formatted_schedule }}

**Coordinator:** {{ $event->creator->first_name }} {{ $event->creator->last_name }}
@endcomponent

Please click the button below to add this {{ $event->is_meeting ? 'meeting' : 'event' }} to your Google Calendar.

@component('mail::button', ['url' => $gcalLink, 'color' => 'primary'])
Add to Calendar
@endcomponent

Thank you for your prompt attention to this matter.

Respectfully,<br>
**City Information Technology Office**<br>
*CITO Workspace Operations*
@endcomponent
