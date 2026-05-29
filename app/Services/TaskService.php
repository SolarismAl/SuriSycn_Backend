<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Jobs\TaskAssignmentNotificationJob;

class TaskService extends BaseService
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask(array $data)
    {
        $task = $this->taskRepository->create($data);

        if (!empty($data['assigned_to'])) {
            // Dispatch notification job
            TaskAssignmentNotificationJob::dispatch($task->id);
        }

        return $task;
    }

    public function updateTask(string $id, array $data)
    {
        $oldTask = $this->taskRepository->findOrFail($id);
        $this->taskRepository->update($data, $id);
        
        $newTask = $this->taskRepository->findOrFail($id);

        if (!empty($data['assigned_to']) && $oldTask->assigned_to !== $data['assigned_to']) {
            TaskAssignmentNotificationJob::dispatch($newTask->id);
        }

        return $newTask;
    }
}
