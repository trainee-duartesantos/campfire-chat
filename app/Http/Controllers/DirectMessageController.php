<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectMessageController extends Controller
{
    public function show(User $user)
    {
        // Não permitir falar consigo próprio
        abort_if($user->id === Auth::id(), 403);

        // Buscar conversa (enviei OU recebi)
        $messages = Message::with('user')
            ->where(function ($q) use ($user) {
                $q->where('user_id', Auth::id())
                  ->where('messageable_id', $user->id)
                  ->where('messageable_type', User::class);
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('messageable_id', Auth::id())
                  ->where('messageable_type', User::class);
            })
            ->orderBy('created_at')
            ->get();

        return view('messages.direct', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        Message::create([
            'user_id'          => Auth::id(),
            'content'          => $request->content,
            'messageable_id'   => $user->id,
            'messageable_type' => User::class,
        ]);

        return redirect()->route('messages.direct.show', $user);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        broadcast(new DirectMessageSent($message))->toOthers();

        return response()->json($message);
    }

}
