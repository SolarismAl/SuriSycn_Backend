<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TaskDueDateWarningJob implements ShouldQueue
{
    use Queueable;

    protected string $taskId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $task = \App\Models\Task::find($this->taskId);
        if ($task && $task->assigned_to && $task->status !== 'completed') {
            // Logic to send due date warning
        }
    }
}
