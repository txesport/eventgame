<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Auth::user()->groups()
            ->with('creator', 'users')
            ->orderBy('groups.created_at', 'desc')
            ->get();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create([
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'owner_id'    => Auth::id(),    // ← ASSURER CE CHAMP
        ]);

        $group->addMember(Auth::user());

        return redirect()->route('groups.show', $group)
                        ->with('success', 'Groupe créé avec succès !');
    }

    public function show(Group $group)
    {
        if (!$group->isMember(Auth::user())) {
            abort(403, 'Vous n\'êtes pas membre de ce groupe.');
        }

        $group->load(['users', 'events' => function ($query) {
        $query->orderBy('events.created_at', 'desc');
        }]);

        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres groupes.');
        }

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres groupes.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group->update($validated);

        return redirect()->route('groups.show', $group)->with('success', 'Groupe mis à jour avec succès !');
    }

    public function destroy(Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres groupes.');
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Groupe supprimé avec succès !');
    }

    public function join(Request $request)
    {
        $validated = $request->validate([
            'invitation_code' => 'required|string|exists:groups,invitation_code',
        ]);

        $group = Group::where('invitation_code', $validated['invitation_code'])->firstOrFail();
        
        if ($group->isMember(Auth::user())) {
            return redirect()->route('groups.show', $group)->with('info', 'Vous êtes déjà membre de ce groupe.');
        }

        $group->addMember(Auth::user());

        return redirect()->route('groups.show', $group)->with('success', 'Vous avez rejoint le groupe avec succès !');
    }

    public function leave(Group $group)
    {
        if (!$group->isMember(Auth::user())) {
            abort(403, 'Vous n\'êtes pas membre de ce groupe.');
        }

        if ($group->created_by === Auth::id()) {
            return redirect()->back()->with('error', 'Le créateur du groupe ne peut pas le quitter.');
        }

        $group->removeMember(Auth::user());

        return redirect()->route('groups.index')->with('success', 'Vous avez quitté le groupe.');
    }

    public function removeMember(Group $group, User $user)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403, 'Seul le créateur peut retirer des membres.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous retirer vous-même.');
        }

        $group->removeMember($user);

        return redirect()->back()->with('success', 'Membre retiré du groupe.');
    }
}
