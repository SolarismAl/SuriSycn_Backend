<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    
    public function paginate(int $perPage = 15, array $columns = ['*']);
    
    public function create(array $data): Model;
    
    public function update(array $data, string $id): bool;
    
    public function delete(string $id): bool;
    
    public function find(string $id, array $columns = ['*']): ?Model;
    
    public function findOrFail(string $id, array $columns = ['*']): Model;
}
