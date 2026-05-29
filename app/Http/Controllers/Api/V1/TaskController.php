<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\Request;

class TaskController extends BaseApiController
{
    protected TaskService $taskService;
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskService $taskService, TaskRepositoryInterface $taskRepository)
    {
        $this->taskService = $taskService;
        $this->taskRepository = $taskRepository;
    }

    public function index(Request $request)
    {
        $tasks = $this->taskRepository->all();
        return $this->successResponse(TaskResource::collection($tasks), 'Tasks retrieved successfully');
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $task = $this->taskService->createTask($data);
        return $this->successResponse(new TaskResource($task), 'Task created successfully', 201);
    }

    public function show(string $id)
    {
        $task = $this->taskRepository->findOrFail($id);
        return $this->successResponse(new TaskResource($task), 'Task retrieved successfully');
    }

    public function update(UpdateTaskRequest $request, string $id)
    {
        $data = $request->validated();
        
        $task = $this->taskService->updateTask($id, $data);
        return $this->successResponse(new TaskResource($task), 'Task updated successfully');
    }

    public function destroy(string $id)
    {
        $this->taskRepository->delete($id);
        return $this->successResponse(null, 'Task deleted successfully');
    }
}
