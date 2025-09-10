<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_date_id',
        'user_id',
        'vote', // boolean : true/false
    ];

    protected $casts = [
        'vote' => 'boolean',
    ];

    public function eventDate()
    {
        return $this->belongsTo(EventDate::class, 'event_date_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
