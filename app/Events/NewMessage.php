<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
// Remplacer ShouldBroadcast par ShouldBroadcastNow
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastWith()
    {
        return [
            'id'         => $this->message->id,
            'content'    => $this->message->content,
            'user'       => [
                'id'         => $this->message->user->id,
                'name'       => $this->message->user->name,
                'avatar_url' => $this->message->user->avatar_url,
            ],
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('group.' . $this->message->group_id);
    }
}
