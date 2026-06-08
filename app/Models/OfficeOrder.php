<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OfficeOrder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'memo_number',
        'subject',
        'description',
        'date_issued',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'date_issued' => 'date',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'office_order_user');
    }

    public function ctoEntries()
    {
        return $this->hasMany(CtoEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
