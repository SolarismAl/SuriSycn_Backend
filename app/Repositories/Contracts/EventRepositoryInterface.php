<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface extends BaseRepositoryInterface
{
    public function getByDepartment(string $departmentId): Collection;
    
    public function getUpcomingEvents(int $limit = 10): Collection;
    
    public function findConflictingEvents(string $startDate, string $endDate, ?string $excludeEventId = null): Collection;
}
