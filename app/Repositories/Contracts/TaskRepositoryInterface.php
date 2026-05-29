<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface extends BaseRepositoryInterface
{
    public function getTasksByAssignee(string $userId): Collection;
    
    public function getTasksByCreator(string $userId): Collection;
}
