<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventDate;
use App\Models\EventActivity;
use App\Models\DateVote;
use App\Models\ActivityVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function voteDate(Request $request, EventDate $eventDate)
    {
        $event = $eventDate->event;
        
        // Vérifier que l'utilisateur est membre du groupe
        if (!$event->group->isMember(Auth::user())) {
            abort(403, 'Vous devez être membre du groupe pour voter.');
        }

        $validated = $request->validate([
            'vote' => 'required|boolean'
        ]);

        DateVote::updateOrCreate(
            [
                'event_date_id' => $eventDate->id,
                'user_id' => Auth::id()
            ],
            ['vote' => $validated['vote']]
        );

        // Retourner les résultats mis à jour
        return response()->json([
            'success' => true,
            'results' => [
                'yes_votes' => $eventDate->yes_votes,
                'no_votes' => $eventDate->no_votes,
                'total_votes' => $eventDate->total_votes,
                'user_vote' => $validated['vote']
            ]
        ]);
    }

    public function voteActivity(Request $request, EventActivity $eventActivity)
    {
        $event = $eventActivity->event;
        
        // Vérifier que l'utilisateur est membre du groupe
        if (!$event->group->isMember(Auth::user())) {
            abort(403, 'Vous devez être membre du groupe pour voter.');
        }

        $validated = $request->validate([
            'vote' => 'required|boolean'
        ]);

        ActivityVote::updateOrCreate(
            [
                'event_activity_id' => $eventActivity->id,
                'user_id' => Auth::id()
            ],
            ['vote' => $validated['vote']]
        );

        // Retourner les résultats mis à jour
        return response()->json([
            'success' => true,
            'results' => [
                'yes_votes' => $eventActivity->yes_votes,
                'no_votes' => $eventActivity->no_votes,
                'total_votes' => $eventActivity->total_votes,
                'user_vote' => $validated['vote']
            ]
        ]);
    }

    public function removeVoteDate(EventDate $eventDate)
    {
        DateVote::where('event_date_id', $eventDate->id)
               ->where('user_id', Auth::id())
               ->delete();

        return response()->json([
            'success' => true,
            'results' => [
                'yes_votes' => $eventDate->yes_votes,
                'no_votes' => $eventDate->no_votes,
                'total_votes' => $eventDate->total_votes,
                'user_vote' => null
            ]
        ]);
    }

    public function removeVoteActivity(EventActivity $eventActivity)
    {
        ActivityVote::where('event_activity_id', $eventActivity->id)
                   ->where('user_id', Auth::id())
                   ->delete();

        return response()->json([
            'success' => true,
            'results' => [
                'yes_votes' => $eventActivity->yes_votes,
                'no_votes' => $eventActivity->no_votes,
                'total_votes' => $eventActivity->total_votes,
                'user_vote' => null
            ]
        ]);
    }
}
