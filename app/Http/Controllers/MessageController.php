<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewMessage;

class MessageController extends Controller
{
    public function index(Group $group)
    {
        if (! $group->isMember(Auth::user())) {
            abort(403);
        }

        $messages = $group->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, Group $group)
    {
        if (! $group->isMember(Auth::user())) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        $message->load('user');
        broadcast(new NewMessage($message))->toOthers();

        return response()->json($message, 201);
    }
}
