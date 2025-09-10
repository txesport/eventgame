<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\ActivityVote;

class EventActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'category',
        'description',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function votes()
    {
        return $this->hasMany(ActivityVote::class, 'event_activity_id');
    }

    public function getYesVotesAttribute(): int
    {
        return $this->votes()->where('vote', 'yes')->count();
    }
}
