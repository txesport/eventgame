<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityVote extends Model
{
    use HasFactory;

    protected $table = 'event_activity_votes'; // ← Nom correct de la table

    protected $fillable = [
        'event_activity_id',
        'user_id',
        'vote_type',
    ];

    public function eventActivity()
    {
        return $this->belongsTo(EventActivity::class, 'event_activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
