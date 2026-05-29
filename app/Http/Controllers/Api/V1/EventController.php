<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Services\EventService;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends BaseApiController
{
    protected EventService $eventService;
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventService $eventService, EventRepositoryInterface $eventRepository)
    {
        $this->eventService = $eventService;
        $this->eventRepository = $eventRepository;
    }

    public function index(Request $request)
    {
        $events = $this->eventRepository->all();
        return $this->successResponse(EventResource::collection($events), 'Events retrieved successfully');
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $event = $this->eventService->createEvent($data, $data['tagged_users'] ?? []);
        return $this->successResponse(new EventResource($event), 'Event created successfully', 201);
    }

    public function show(string $id)
    {
        $event = $this->eventRepository->findOrFail($id)->load('taggedUsers');
        return $this->successResponse(new EventResource($event), 'Event retrieved successfully');
    }

    public function update(UpdateEventRequest $request, string $id)
    {
        $data = $request->validated();
        $taggedUsers = isset($data['tagged_users']) ? $data['tagged_users'] : null;
        unset($data['tagged_users']);

        $event = $this->eventService->updateEvent($id, $data, $taggedUsers);
        return $this->successResponse(new EventResource($event), 'Event updated successfully');
    }

    public function destroy(string $id)
    {
        $this->eventRepository->delete($id);
        return $this->successResponse(null, 'Event deleted successfully');
    }
}
