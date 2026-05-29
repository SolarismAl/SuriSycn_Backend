<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface extends BaseRepositoryInterface
{
    public function getConflictingReservations(string $roomName, string $startTime, string $endTime, ?string $excludeId = null): Collection;
    
    public function getPendingReservations(): Collection;
}
