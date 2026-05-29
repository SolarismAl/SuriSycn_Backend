<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AnnouncementRepositoryInterface extends BaseRepositoryInterface
{
    public function getPublishedAnnouncements(int $limit = 10): Collection;
}
