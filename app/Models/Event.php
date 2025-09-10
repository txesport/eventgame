<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'group_id',
        'creator_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function dates()
    {
        return $this->hasMany(EventDate::class);
    }

    public function activities()
    {
        return $this->hasMany(EventActivity::class);
    }

    public function photos()
    {
        return $this->hasMany(EventPhoto::class)->orderBy('created_at', 'desc');
    }

    public function getCoverPhotoAttribute()
    {
        return $this->photos()->first();
    }
}
