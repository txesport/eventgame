<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;
use App\Models\Group;
use App\Models\User;
use App\Models\EventDate;
use App\Models\EventActivity;
use App\Models\EventPhoto;

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
        'created_at' => 'datetime',  // corrigé => au lieu de =
        'updated_at' => 'datetime',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

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
