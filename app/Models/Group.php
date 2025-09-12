<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'description',
    'owner_id', // ou created_by
    'invitation_code',  // Ajouter cette ligne
];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            if (empty($group->invitation_code)) {
                $group->invitation_code = strtoupper(Str::random(8));
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
    public function messages()
{
    return $this->hasMany(Message::class);
}


    public function removeMember(User $user): void
    {
        $this->users()->detach($user->id);
    }
    public function creator()
{
    // Préciser owner_id (et non created_by)
    return $this->belongsTo(User::class, 'owner_id');
}


    // Ajouter un membre
    public function addMember(User $user): void
    {
        $this->users()->attach($user->id);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
