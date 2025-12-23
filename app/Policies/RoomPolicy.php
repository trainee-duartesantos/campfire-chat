<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Room $room): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Room $room): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Room $room): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Room $room): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Room $room): bool
    {
        return false;
    }

    public function manage(User $user, Room $room): bool
    {
        return $user->isAdmin() || $room->created_by === $user->id;
    }
    // Admin convida user
    public function invite(User $user, Room $room): bool
    {
        return $room->isAdmin($user);
    }
    public function show(Room $room)
    {
        abort_unless($room->users->contains(auth()->id()), 403);

        $users = User::where('id', '!=', auth()->id())->get();

        return view('rooms.show', compact('room', 'users'));
    }

}
