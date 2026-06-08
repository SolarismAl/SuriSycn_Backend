<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->task->due_date ? $this->task->due_date->format('M d, Y h:i A') : 'No due date';
        
        return (new MailMessage)
                    ->subject('Task Assigned: ' . $this->task->title)
                    ->greeting('Hello ' . $notifiable->first_name . ',')
                    ->line('You have been assigned a new task.')
                    ->line('**Title:** ' . $this->task->title)
                    ->line('**Description:** ' . $this->task->description)
                    ->line('**Due Date:** ' . $dueDate)
                    ->line('**Priority:** ' . ucfirst($this->task->priority))
                    ->action('View Task', url('/tasks/' . $this->task->id))
                    ->line('Sender: ' . $this->task->creator->first_name . ' ' . $this->task->creator->last_name)
                    ->salutation("Best regards,\nCITO Workspace Operations");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => 'You were assigned a task: ' . $this->task->title,
        ];
    }
}
