<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CtoEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'office_order_id',
        'type',
        'date',
        'hours',
        'reason',
        'status',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function officeOrder()
    {
        return $this->belongsTo(OfficeOrder::class);
    }
}
