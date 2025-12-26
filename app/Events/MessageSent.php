<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('user');
    }

    public function broadcastOn(): Channel
    {
        // Canal por sala (chat público)
        return new Channel('room.' . $this->message->messageable_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
