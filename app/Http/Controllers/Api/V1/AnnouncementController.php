<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Services\AnnouncementService;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use Illuminate\Http\Request;

class AnnouncementController extends BaseApiController
{
    protected AnnouncementService $announcementService;
    protected AnnouncementRepositoryInterface $announcementRepository;

    public function __construct(AnnouncementService $announcementService, AnnouncementRepositoryInterface $announcementRepository)
    {
        $this->announcementService = $announcementService;
        $this->announcementRepository = $announcementRepository;
    }

    public function index(Request $request)
    {
        // For standard users, we might want to only fetch published ones.
        // The admin can fetch all.
        if ($request->user() && $request->user()->role === 'admin') {
            $announcements = $this->announcementRepository->all();
        } else {
            $announcements = $this->announcementRepository->getPublishedAnnouncements();
        }
        
        return $this->successResponse(AnnouncementResource::collection($announcements), 'Announcements retrieved successfully');
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $announcement = $this->announcementService->createAnnouncement($data);
        return $this->successResponse(new AnnouncementResource($announcement), 'Announcement created successfully', 201);
    }

    public function show(string $id)
    {
        $announcement = $this->announcementRepository->findOrFail($id);
        return $this->successResponse(new AnnouncementResource($announcement), 'Announcement retrieved successfully');
    }

    public function update(UpdateAnnouncementRequest $request, string $id)
    {
        $data = $request->validated();
        
        $announcement = $this->announcementRepository->update($data, $id);
        
        // Fetch fresh model
        $announcement = $this->announcementRepository->findOrFail($id);
        return $this->successResponse(new AnnouncementResource($announcement), 'Announcement updated successfully');
    }

    public function destroy(string $id)
    {
        $this->announcementRepository->delete($id);
        return $this->successResponse(null, 'Announcement deleted successfully');
    }
}
