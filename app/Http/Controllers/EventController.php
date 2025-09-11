<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\EventDate;
use App\Models\EventActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with([
            'group',
            'creator',
            'photos',
            'dates',
            'activities',
            'expenses',      // AJOUTÉ: pour éviter les erreurs dans index.blade.php
        ])
        ->whereHas('group.users', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $groups = Auth::user()->groups;
        
        return view('events.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
            'dates' => ['required', 'array', 'min:1'],
            'dates.*' => ['required', 'date', 'after:now'],
            'activities' => ['required', 'array', 'min:1'],
            'activities.*.name' => ['required', 'string', 'max:255'],
            'activities.*.category' => ['nullable', 'string', 'max:100'],
            'activities.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        $group = Group::findOrFail($validated['group_id']);
        if (!$group->users->contains(Auth::id())) {
            abort(403, 'Vous ne pouvez pas créer un événement dans ce groupe.');
        }

        $event = Event::create([
            'name' => $validated['name'],
            'group_id' => $validated['group_id'],
            'creator_id' => Auth::id(),
            'status' => 'planning',
        ]);

        foreach ($validated['dates'] as $date) {
            EventDate::create([
                'event_id' => $event->id,
                'date_time' => $date,
            ]);
        }

        foreach ($validated['activities'] as $activity) {
            EventActivity::create([
                'event_id' => $event->id,
                'name' => $activity['name'],
                'category' => $activity['category'],
                'description' => $activity['description'],
            ]);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Événement créé avec succès !');
    }

    public function show(Event $event)
    {
        if (!$event->group->users->contains(Auth::id())) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $event->load([
            'group.users', 
            'creator', 
            'dates.votes', 
            'activities.votes', 
            'photos.user',
            'expenses.payer'  // AJOUTÉ: pour charger les dépenses avec le payeur
        ]);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        if ($event->creator_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas modifier cet événement.');
        }

        $groups = Auth::user()->groups;
        $event->load(['dates', 'activities']);

        return view('events.edit', compact('event', 'groups'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->creator_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas modifier cet événement.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
            'dates' => ['required', 'array', 'min:1'],
            'dates.*' => ['required', 'date', 'after:now'],
            'activities' => ['required', 'array', 'min:1'],
            'activities.*.name' => ['required', 'string', 'max:255'],
            'activities.*.category' => ['nullable', 'string', 'max:100'],
            'activities.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        $event->update([
            'name' => $validated['name'],
            'group_id' => $validated['group_id'],
        ]);

        $event->dates()->delete();
        $event->activities()->delete();

        foreach ($validated['dates'] as $date) {
            EventDate::create([
                'event_id' => $event->id,
                'date_time' => $date,
            ]);
        }

        foreach ($validated['activities'] as $activity) {
            EventActivity::create([
                'event_id' => $event->id,
                'name' => $activity['name'],
                'category' => $activity['category'],
                'description' => $activity['description'],
            ]);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Événement mis à jour avec succès !');
    }

    public function destroy(Event $event)
    {
        if ($event->creator_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas supprimer cet événement.');
        }

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Événement supprimé avec succès.');
    }
}