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
        $rooms = Auth::user()
            ->rooms()
            ->latest()
            ->get();

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
        // Garantir que o utilizador pertence à sala
        abort_unless(
            $room->users->contains(Auth::id()),
            403
        );

        return view('rooms.show', compact('room'));
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

}
