<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'final_date_id',
        'final_activities',
        'summary_notes',
        'is_finalized',
        'reminder_sent_at'
    ];

    protected $casts = [
        'final_activities' => 'array',
        'is_finalized' => 'boolean',
        'reminder_sent_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function finalDate(): BelongsTo
    {
        return $this->belongsTo(EventDate::class, 'final_date_id');
    }

    public function getFinalActivitiesModelsAttribute()
    {
        if (!$this->final_activities) {
            return collect();
        }
        
        return EventActivity::whereIn('id', $this->final_activities)->get();
    }

    public function needsReminder(): bool
    {
        if (!$this->is_finalized || !$this->finalDate || $this->reminder_sent_at) {
            return false;
        }

        $reminderTime = $this->finalDate->proposed_date->subHour();
        return now()->isAfter($reminderTime);
    }

    public function generateSummaryText(): string
    {
        $summary = "📅 Résumé de l'événement : " . $this->event->name . "\n\n";
        
        if ($this->finalDate) {
            $summary .= "🕒 Date finale : " . $this->finalDate->proposed_date->format('d/m/Y à H:i') . "\n";
        }
        
        if ($this->event->location) {
            $summary .= "📍 Lieu : " . $this->event->location . "\n";
        }
        
        $summary .= "👥 Participants : " . $this->event->group->users->pluck('name')->join(', ') . "\n\n";
        
        if ($this->final_activities_models->isNotEmpty()) {
            $summary .= "🎯 Activités retenues :\n";
            foreach ($this->final_activities_models as $activity) {
                $summary .= "• " . $activity->activity_name . "\n";
            }
            $summary .= "\n";
        }
        
        if ($this->event->total_expenses > 0) {
            $summary .= "💰 Résumé financier :\n";
            $summary .= "Total des dépenses : " . number_format($this->event->total_expenses, 2, ',', ' ') . " €\n";
            
            $expenseSummary = $this->event->getExpenseSummary();
            foreach ($expenseSummary as $userId => $data) {
                if ($data['balance'] != 0) {
                    $status = $data['balance'] > 0 ? 'à recevoir' : 'à payer';
                    $summary .= "• " . $data['user']->name . " : " . 
                               number_format(abs($data['balance']), 2, ',', ' ') . " € " . $status . "\n";
                }
            }
        }
        
        if ($this->summary_notes) {
            $summary .= "\n📝 Notes :\n" . $this->summary_notes;
        }
        
        return $summary;
    }
}
