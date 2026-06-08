<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\LogsActivity;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, HasUuids, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'recurrence',
        'color',
        'created_by',
        'department_id',
        'is_meeting',
        'external_participants',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_meeting' => \App\Casts\PgBooleanCast::class,
        'external_participants' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tags()
    {
        return $this->hasMany(EventUserTag::class);
    }

    public function taggedUsers()
    {
        return $this->belongsToMany(User::class, 'event_user_tags')
                    ->using(EventUserTag::class)
                    ->withPivot('id')
                    ->withTimestamps();
    }

    public function getFormattedScheduleAttribute()
    {
        $start = $this->start_date;
        $end = $this->end_date;

        if ($start->isSameDay($end)) {
            return $start->format('F j, Y g:i A') . ' - ' . $end->format('g:i A');
        }

        return $start->format('F j, Y g:i A') . ' to ' . $end->format('F j, Y g:i A');
    }
}
