<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'paid_by',
        'description',
        'amount',
        'participants'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'participants' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function getParticipantUsersAttribute()
    {
        return User::whereIn('id', $this->participants)->get();
    }

    public function getAmountPerPersonAttribute()
    {
        $participantCount = count($this->participants);
        return $participantCount > 0 ? $this->amount / $participantCount : 0;
    }
}
