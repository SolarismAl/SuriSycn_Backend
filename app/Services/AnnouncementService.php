<?php

namespace App\Services;

use App\Repositories\Contracts\AnnouncementRepositoryInterface;

class AnnouncementService extends BaseService
{
    protected AnnouncementRepositoryInterface $announcementRepository;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository)
    {
        $this->announcementRepository = $announcementRepository;
    }

    public function createAnnouncement(array $data)
    {
        $announcement = $this->announcementRepository->create($data);

        if (!empty($data['published_at']) && $data['published_at'] <= now()) {
            $users = \App\Models\User::all();
            \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\AnnouncementPublishedNotification($announcement));
        }

        return $announcement;
    }

    public function publish(string $id)
    {
        $announcement = $this->announcementRepository->update(['published_at' => now()], $id);
        $announcement = $this->announcementRepository->findOrFail($id);
        
        $users = \App\Models\User::all();
        \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\AnnouncementPublishedNotification($announcement));

        return $this->announcementRepository->findOrFail($id);
    }
}
