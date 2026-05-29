<?php

namespace App\Services;

use App\Repositories\Contracts\ReservationRepositoryInterface;
use Exception;

class ReservationService extends BaseService
{
    protected ReservationRepositoryInterface $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function createReservation(array $data)
    {
        // Check for conflicts
        $conflicts = $this->reservationRepository->getConflictingReservations($data['room_name'], $data['start_time'], $data['end_time']);
        
        if ($conflicts->isNotEmpty()) {
            throw new Exception("The room is already reserved or pending during this time.");
        }

        $data['status'] = 'pending';
        return $this->reservationRepository->create($data);
    }

    public function updateStatus(string $id, string $status, string $adminId)
    {
        $reservation = $this->reservationRepository->findOrFail($id);

        if ($status === 'approved') {
            // Double check conflict before approving just in case
            $conflicts = $this->reservationRepository->getConflictingReservations(
                $reservation->room_name,
                $reservation->start_time,
                $reservation->end_time,
                $reservation->id
            )->where('status', 'approved');

            if ($conflicts->isNotEmpty()) {
                throw new Exception("Cannot approve. The room is already approved for another reservation during this time.");
            }
        }

        $this->reservationRepository->update([
            'status'      => $status,
            'approved_by' => $adminId
        ], $id);

        $reservation = $this->reservationRepository->findOrFail($id);

        \App\Jobs\ReservationNotificationJob::dispatch($reservation->id);

        return $reservation;
    }

    public function updateReservation(string $id, array $data)
    {
        $reservation = $this->reservationRepository->findOrFail($id);

        $roomName  = $data['room_name']  ?? $reservation->room_name;
        $startTime = $data['start_time'] ?? $reservation->start_time;
        $endTime   = $data['end_time']   ?? $reservation->end_time;

        // Check conflicts excluding the current reservation
        $conflicts = $this->reservationRepository->getConflictingReservations(
            $roomName,
            $startTime,
            $endTime,
            $id
        )->where('status', 'approved');

        if ($conflicts->isNotEmpty()) {
            throw new Exception("The room already has an approved reservation during this time.");
        }

        // Reset to pending when room/time changes, collect only allowed fields
        $updateData = array_intersect_key($data, array_flip(['room_name', 'start_time', 'end_time']));
        $updateData['status'] = 'pending';

        return $this->reservationRepository->update($updateData, $id);
    }
}
