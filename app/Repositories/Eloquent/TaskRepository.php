<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function getTasksByAssignee(string $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)->orderBy('due_date', 'asc')->get();
    }

    public function getTasksByCreator(string $userId): Collection
    {
        return $this->model->where('created_by', $userId)->orderBy('created_at', 'desc')->get();
    }
}
