<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // Vérifier que l'utilisateur est membre du groupe
        if (!$event->group->isMember(Auth::user())) {
            abort(403, 'Vous devez être membre du groupe pour ajouter une dépense.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:99999.99',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id'
        ]);

        // Vérifier que tous les participants sont membres du groupe
        $groupMemberIds = $event->group->users->pluck('id')->toArray();
        $invalidParticipants = array_diff($validated['participants'], $groupMemberIds);
        
        if (!empty($invalidParticipants)) {
            return back()->withErrors(['participants' => 'Tous les participants doivent être membres du groupe.']);
        }

        Expense::create([
            'event_id' => $event->id,
            'paid_by' => Auth::id(),
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'participants' => $validated['participants']
        ]);

        return back()->with('success', 'Dépense ajoutée avec succès !');
    }

    public function update(Request $request, Expense $expense)
    {
        // Vérifier que l'utilisateur est le créateur de la dépense
        if ($expense->paid_by !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres dépenses.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:99999.99',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id'
        ]);

        // Vérifier que tous les participants sont membres du groupe
        $groupMemberIds = $expense->event->group->users->pluck('id')->toArray();
        $invalidParticipants = array_diff($validated['participants'], $groupMemberIds);
        
        if (!empty($invalidParticipants)) {
            return back()->withErrors(['participants' => 'Tous les participants doivent être membres du groupe.']);
        }

        $expense->update($validated);

        return back()->with('success', 'Dépense mise à jour avec succès !');
    }

    public function destroy(Expense $expense)
    {
        // Vérifier que l'utilisateur est le créateur de la dépense
        if ($expense->paid_by !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres dépenses.');
        }

        $expense->delete();

        return back()->with('success', 'Dépense supprimée avec succès !');
    }

    public function getBalance(Event $event)
    {
        if (!$event->group->isMember(Auth::user())) {
            abort(403);
        }

        return response()->json([
            'summary' => $event->getExpenseSummary(),
            'total' => $event->total_expenses
        ]);
    }
}
