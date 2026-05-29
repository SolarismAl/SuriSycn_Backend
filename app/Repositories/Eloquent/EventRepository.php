<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function getByDepartment(string $departmentId): Collection
    {
        return $this->model->where('department_id', $departmentId)->get();
    }

    public function getUpcomingEvents(int $limit = 10): Collection
    {
        return $this->model->where('start_date', '>=', now())
                           ->orderBy('start_date', 'asc')
                           ->limit($limit)
                           ->get();
    }

    public function findConflictingEvents(string $startDate, string $endDate, ?string $excludeEventId = null): Collection
    {
        $query = $this->model->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
        });

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        return $query->get();
    }
}
