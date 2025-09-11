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
        'created_at' => 'datetime', // CORRIGÉ: => au lieu de =
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

    // NOUVELLES MÉTHODES POUR LES DÉPENSES
    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getExpenseSummary()
    {
        $expenses = $this->expenses()->with('payer')->get();
        $groupMembers = $this->group->users;
        $summary = [];

        // Initialiser le tableau de résumé pour chaque membre
        foreach ($groupMembers as $member) {
            $summary[$member->id] = [
                'user' => $member,
                'paid' => 0,
                'owes' => 0,
                'balance' => 0,
            ];
        }

        // Calculer ce que chaque personne a payé et doit
        foreach ($expenses as $expense) {
            $payerId = $expense->paid_by;
            $amountPerPerson = $expense->amount_per_person;
            
            // Ajouter au total payé par cette personne
            if (isset($summary[$payerId])) {
                $summary[$payerId]['paid'] += $expense->amount;
            }

            // Répartir la dette entre tous les participants
            foreach ($expense->participants as $participantId) {
                if (isset($summary[$participantId])) {
                    $summary[$participantId]['owes'] += $amountPerPerson;
                }
            }
        }

        // Calculer le solde final (ce qu'on a payé - ce qu'on doit)
        foreach ($summary as $userId => &$data) {
            $data['balance'] = $data['paid'] - $data['owes'];
        }

        return collect($summary)->values();
    }

    public function getUserBalance(User $user)
    {
        $summary = $this->getExpenseSummary();
        return $summary->firstWhere('user.id', $user->id);
    }
}