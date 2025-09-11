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
        ->get()
        ->map(function($msg) {
            return [
                'id' => $msg->id,
                'content' => $msg->content,
                'user' => [
                    'id' => $msg->user->id,
                    'name' => $msg->user->name,
                    'avatar_url' => $msg->user->avatar_url,
                ],
                'created_at' => $msg->created_at->toDateTimeString(),
            ];
        });

    return response()->json($messages);
}

  public function store(Request $request, Group $group)
{
    if (! $group->isMember(Auth::user())) abort(403);

    $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    $message = Message::create([
        'group_id' => $group->id,
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    $message->load('user');

    broadcast(new NewMessage($message))->toOthers(); // diffusion

    return response()->json([
        'id' => $message->id,
        'content' => $message->content,
        'user' => [
            'id' => $message->user->id,
            'name' => $message->user->name,
            'avatar_url' => $message->user->avatar_url,
        ],
        'created_at' => $message->created_at->toDateTimeString(),
    ], 201);
}


}
