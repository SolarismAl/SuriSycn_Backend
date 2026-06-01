<?php

namespace App\Services;

use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class EventService extends BaseService
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function createEvent(array $data, ?array $taggedUserIds = [])
    {
        // Simple conflict detection (if required for events, usually more for rooms, but we check anyway)
        $conflicts = $this->eventRepository->findConflictingEvents($data['start_date'], $data['end_date']);
        if ($conflicts->isNotEmpty()) {
            // Depending on requirements, we might just warn, but let's allow overlapping events
            // if they are just standard calendar events, unlike meeting rooms.
        }

        DB::beginTransaction();
        try {
            $event = $this->eventRepository->create($data);

            if (!empty($taggedUserIds)) {
                $syncData = [];
                foreach ($taggedUserIds as $userId) {
                    $syncData[$userId] = ['id' => \Illuminate\Support\Str::uuid()->toString()];
                }
                $event->taggedUsers()->sync($syncData);

                // Notify users
                $users = \App\Models\User::whereIn('id', $taggedUserIds)->get();
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\EventInvitationNotification($event));
            }

            // Notify external participants
            if (isset($data['external_participants']) && is_array($data['external_participants']) && !empty($data['external_participants'])) {
                foreach ($data['external_participants'] as $email) {
                    \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\ExternalEventInvitationMail($event));
                }
            }

            DB::commit();
            return $event->load('taggedUsers');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateEvent(string $id, array $data, ?array $taggedUserIds = null)
    {
        DB::beginTransaction();
        try {
            $this->eventRepository->update($data, $id);
            $event = $this->eventRepository->findOrFail($id);

            if ($taggedUserIds !== null) {
                $syncData = [];
                foreach ($taggedUserIds as $userId) {
                    $syncData[$userId] = ['id' => \Illuminate\Support\Str::uuid()->toString()];
                }
                $event->taggedUsers()->sync($syncData);
                
                // Notify users
                $users = \App\Models\User::whereIn('id', $taggedUserIds)->get();
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\EventInvitationNotification($event));
            }

            // Notify external participants
            if (isset($data['external_participants']) && is_array($data['external_participants']) && !empty($data['external_participants'])) {
                foreach ($data['external_participants'] as $email) {
                    \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\ExternalEventInvitationMail($event));
                }
            }

            DB::commit();
            return $event->load('taggedUsers');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
