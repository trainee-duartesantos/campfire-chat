<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request, Room $room)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // Garantir que o utilizador pertence à sala
        if (! $room->users->contains(Auth::id())) {
            abort(403);
        }

        $message = Message::create([
            'user_id'          => Auth::id(),
            'content'          => $request->content,
            'messageable_id'   => $room->id,
            'messageable_type' => Room::class,
        ]);

        event(new MessageSent($message));
        
        return redirect()->route('rooms.show', $room);
    }
}
