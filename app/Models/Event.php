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
        'external_participants',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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
}
