<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_activity_id',
        'user_id',
        'vote'
    ];

    protected $casts = [
        'vote' => 'boolean',
    ];

    public function eventActivity(): BelongsTo
    {
        return $this->belongsTo(EventActivity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
