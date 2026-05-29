<?php

namespace App\Repositories\Eloquent;

use App\Models\Announcement;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AnnouncementRepository extends BaseRepository implements AnnouncementRepositoryInterface
{
    public function __construct(Announcement $model)
    {
        parent::__construct($model);
    }

    public function getPublishedAnnouncements(int $limit = 10): Collection
    {
        return $this->model->whereNotNull('published_at')
                           ->where('published_at', '<=', now())
                           ->orderBy('published_at', 'desc')
                           ->limit($limit)
                           ->get();
    }
}
