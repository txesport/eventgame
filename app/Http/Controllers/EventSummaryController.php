<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSummary;
use App\Models\EventDate;
use App\Models\EventActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventSummaryController extends Controller
{
    public function show(Event $event)
    {
        if (!$event->group->isMember(Auth::user())) {
            abort(403, 'Vous devez être membre du groupe pour voir ce résumé.');
        }

        $event->load(['group', 'creator', 'dates', 'activities', 'expenses', 'summary.finalDate']);
        
        $mostVotedDate = $event->getMostVotedDate();
        $mostVotedActivities = $event->getMostVotedActivities(3);
        $expenseSummary = $event->getExpenseSummary();

        return view('events.summary', compact('event', 'mostVotedDate', 'mostVotedActivities', 'expenseSummary'));
    }

    public function finalize(Request $request, Event $event)
    {
        if ($event->created_by !== Auth::id()) {
            abort(403, 'Seul le créateur peut finaliser l\'événement.');
        }

        if (!$event->canBeFinalized()) {
            return back()->with('error', 'Cet événement ne peut pas être finalisé.');
        }

        $validated = $request->validate([
            'final_date_id' => 'nullable|exists:event_dates,id',
            'final_activities' => 'nullable|array',
            'final_activities.*' => 'exists:event_activities,id',
            'summary_notes' => 'nullable|string|max:1000'
        ]);

        // Vérifier que la date appartient à cet événement
        if ($validated['final_date_id']) {
            $finalDate = EventDate::findOrFail($validated['final_date_id']);
            if ($finalDate->event_id !== $event->id) {
                return back()->with('error', 'Date invalide pour cet événement.');
            }
        }

        // Vérifier que les activités appartiennent à cet événement
        if (!empty($validated['final_activities'])) {
            $activities = EventActivity::whereIn('id', $validated['final_activities'])->get();
            foreach ($activities as $activity) {
                if ($activity->event_id !== $event->id) {
                    return back()->with('error', 'Activité invalide pour cet événement.');
                }
            }
        }

        EventSummary::updateOrCreate(
            ['event_id' => $event->id],
            [
                'final_date_id' => $validated['final_date_id'],
                'final_activities' => $validated['final_activities'] ?? null,
                'summary_notes' => $validated['summary_notes'],
                'is_finalized' => true
            ]
        );

        return redirect()->route('events.summary', $event)->with('success', 'Événement finalisé avec succès !');
    }

    public function unfinalize(Event $event)
    {
        if ($event->created_by !== Auth::id()) {
            abort(403, 'Seul le créateur peut modifier la finalisation.');
        }

        if ($event->summary) {
            $event->summary->update(['is_finalized' => false]);
        }

        return redirect()->route('events.summary', $event)->with('success', 'Finalisation annulée.');
    }

    public function sendReminders(Event $event)
    {
        if ($event->created_by !== Auth::id()) {
            abort(403, 'Seul le créateur peut envoyer des rappels.');
        }

        if (!$event->isFinalized() || !$event->summary->finalDate) {
            return back()->with('error', 'L\'événement doit être finalisé avec une date pour envoyer des rappels.');
        }

        // Marquer le rappel comme envoyé
        $event->summary->update(['reminder_sent_at' => now()]);

        // Ici vous pourriez implémenter l'envoi de notifications réelles
        // (email, SMS, push notifications, etc.)
        // Pour ce MVP, on simule juste l'envoi

        return back()->with('success', 'Rappels envoyés à tous les participants !');
    }

    public function export(Event $event)
    {
        if (!$event->group->isMember(Auth::user())) {
            abort(403);
        }

        if (!$event->summary) {
            return back()->with('error', 'Créez d\'abord un résumé pour exporter.');
        }

        $summaryText = $event->summary->generateSummaryText();
        $fileName = 'resume_' . Str::slug($event->name) . '_' . now()->format('Y-m-d') . '.txt';

        return response($summaryText)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function checkReminders()
    {
        // Cette méthode pourrait être appelée par un cron job
        // pour vérifier les événements nécessitant des rappels
        
        $summaries = EventSummary::with(['event', 'finalDate'])
            ->where('is_finalized', true)
            ->whereNotNull('final_date_id')
            ->whereNull('reminder_sent_at')
            ->get();

        $remindersSent = 0;
        foreach ($summaries as $summary) {
            if ($summary->needsReminder()) {
                // Envoyer le rappel (implémentation selon vos besoins)
                $summary->update(['reminder_sent_at' => now()]);
                $remindersSent++;
            }
        }

        return response()->json([
            'reminders_sent' => $remindersSent,
            'message' => "Rappels automatiques vérifiés. {$remindersSent} rappel(s) envoyé(s)."
        ]);
    }
}
