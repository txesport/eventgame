<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\DateVote;

class EventDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'date_time',
        'is_selected',
    ];

    protected $casts = [
        'date_time'   => 'datetime',
        'is_selected' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function votes()
    {
        return $this->hasMany(DateVote::class, 'event_date_id');
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->votes()->count();
    }

    public function getYesVotesAttribute(): int
    {
        return $this->votes()->where('vote', true)->count();
    }
}
