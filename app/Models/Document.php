<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Document extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'type',
        'file_path',
        'size',
        'parent_id',
        'owner_id',
    ];

    protected $appends = ['date', 'owner_name'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    public function getOwnerNameAttribute()
    {
        return $this->owner ? "{$this->owner->first_name} {$this->owner->last_name}" : 'System';
    }
}
