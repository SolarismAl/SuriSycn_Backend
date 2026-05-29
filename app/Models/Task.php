<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\LogsActivity;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, HasUuids, LogsActivity;

    protected $fillable = [
        'task_number',
        'title',
        'description',
        'priority',
        'progress',
        'due_date',
        'assigned_to',
        'created_by',
        'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'progress' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->task_number)) {
                $lastTask = static::whereNotNull('task_number')->orderBy('task_number', 'desc')->first();
                $lastNumber = 0;
                if ($lastTask && preg_match('/TSK-(\d+)/', $lastTask->task_number, $matches)) {
                    $lastNumber = (int)$matches[1];
                }
                $task->task_number = 'TSK-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
