<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'date_time',
        'is_selected',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'is_selected' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
