<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventPhotoController extends Controller
{
    public function store(Request $request, Event $event)
    {
        if (!$event->group->users->contains(Auth::id())) {
            abort(403, 'Vous ne pouvez pas ajouter de photos à cet événement.');
        }

        $request->validate([
            'photos' => ['required', 'array', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:255'],
        ]);

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $index => $photo) {
            $originalName = $photo->getClientOriginalName();
            $path = $photo->store('event_photos/' . $event->id, 'public');
            
            $eventPhoto = $event->photos()->create([
                'user_id' => Auth::id(),
                'path' => $path,
                'original_name' => $originalName,
                'caption' => $request->input("captions.{$index}"),
            ]);

            $uploadedPhotos[] = $eventPhoto;
        }

        return back()->with('success', count($uploadedPhotos) . ' photo(s) ajoutée(s) avec succès.');
    }

    public function destroy(EventPhoto $photo)
    {
        if ($photo->user_id !== Auth::id() && $photo->event->creator_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas supprimer cette photo.');
        }

        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return back()->with('success', 'Photo supprimée avec succès.');
    }

    public function updateCaption(Request $request, EventPhoto $photo)
    {
        if ($photo->user_id !== Auth::id() && $photo->event->creator_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas modifier cette photo.');
        }

        $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $photo->update([
            'caption' => $request->caption,
        ]);

        return back()->with('success', 'Légende mise à jour.');
    }
}
