<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait to listen for Eloquent events
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->logActivity('updated', [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges()
            ]);
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getAttributes());
        });
    }

    /**
     * Create an audit log record
     *
     * @param string $action
     * @param mixed $metadata
     * @return void
     */
    protected function logActivity(string $action, mixed $metadata = null)
    {
        // Don't log if running from console (like seeders) unless we specifically want to
        if (app()->runningInConsole()) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => class_basename($this),
            'metadata' => $metadata ? json_encode($metadata) : null
        ]);
    }
}
