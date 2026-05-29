<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationService;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Http\Request;

class ReservationController extends BaseApiController
{
    protected ReservationService $reservationService;
    protected ReservationRepositoryInterface $reservationRepository;

    public function __construct(ReservationService $reservationService, ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationService = $reservationService;
        $this->reservationRepository = $reservationRepository;
    }

    public function index(Request $request)
    {
        $reservations = $this->reservationRepository->all();
        return $this->successResponse(ReservationResource::collection($reservations), 'Reservations retrieved successfully');
    }

    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();
        $data['requested_by'] = $request->user()->id;

        try {
            $reservation = $this->reservationService->createReservation($data);
            return $this->successResponse(new ReservationResource($reservation), 'Reservation request submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }
    }

    public function show(string $id)
    {
        $reservation = $this->reservationRepository->findOrFail($id);
        return $this->successResponse(new ReservationResource($reservation), 'Reservation retrieved successfully');
    }

    public function update(UpdateReservationRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            // If only status is being changed, use the approval flow
            if (isset($data['status']) && count($data) === 1) {
                $this->reservationService->updateStatus($id, $data['status'], $request->user()->id);
            } else {
                // Otherwise it's an edit of room/time fields
                $this->reservationService->updateReservation($id, $data);
            }
            $reservation = $this->reservationRepository->findOrFail($id);
            return $this->successResponse(new ReservationResource($reservation), 'Reservation updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }
    }


    public function destroy(string $id)
    {
        $this->reservationRepository->delete($id);
        return $this->successResponse(null, 'Reservation deleted successfully');
    }
}
