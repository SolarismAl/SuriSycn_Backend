<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TaskAssignmentNotificationJob implements ShouldQueue
{
    use Queueable;

    protected string $taskId;

    public function __construct(string $taskId)
    {
        $this->taskId = $taskId;
    }

    public function handle(): void
    {
        $task = \App\Models\Task::with('assignee', 'creator')->find($this->taskId);
        if ($task && $task->assignee) {
            $task->assignee->notify(new \App\Notifications\TaskAssignedNotification($task));
        }
    }
}
