<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
   public function index()
    {
        if (auth()->user()->isAdmin()) {
            $rooms = Room::latest()->get();
        } else {
            $rooms = auth()->user()->rooms()->latest()->get();
        }

        return view('rooms.index', compact('rooms'));
    }


    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'avatar' => $request->avatar,
            'created_by' => Auth::id(),
        ]);

        // O criador entra automaticamente na sala
        $room->users()->attach(Auth::id());

        return redirect()->route('rooms.show', $room);
    }

    public function show(Room $room)
    {
        abort_unless(
            $room->users->contains(auth()->id()),
            403
        );

        $users = User::where('id', '!=', auth()->id())->get();

        return view('rooms.show', compact('room', 'users'));
    }

    public function invite(Request $request, Room $room)
    {
        Gate::authorize('invite', $room);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Evitar duplicados
        if (! $room->users()->where('user_id', $request->user_id)->exists()) {
            $room->users()->attach($request->user_id);
        }

        return back()->with('success', 'Utilizador adicionado à sala.');
    }
    

    public function addMember(Request $request, Room $room)
    {
        $this->authorize('manage', $room);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $room->users()->syncWithoutDetaching($request->user_id);

        return back()->with('success', 'Utilizador adicionado à sala.');
    }

    public function removeMember(Room $room, User $user)
    {
        $this->authorize('manage', $room);

        if ($room->created_by === $user->id) {
            return back()->withErrors('Não podes remover o criador da sala.');
        }

        $room->users()->detach($user->id);

        return back()->with('success', 'Utilizador removido da sala.');
    }

    public function destroy(Room $room)
    {
        $this->authorize('manage', $room);

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Sala removida.');
    }

    public function search(Room $room, Request $request)
    {
        $this->authorize('view', $room);

        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $messages = $room->messages()
            ->with('user')
            ->where('content', 'LIKE', "%{$query}%")
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'created_at' => $message->created_at,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'avatar_url' => $message->user->avatar_url,
                    ],
                ];
            });

        return response()->json($messages);
    }


}
