<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DateVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_date_id',
        'user_id',
        'vote'
    ];

    protected $casts = [
        'vote' => 'boolean',
    ];

    public function eventDate(): BelongsTo
    {
        return $this->belongsTo(EventDate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
