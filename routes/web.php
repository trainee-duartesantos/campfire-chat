<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectMessageController;
use App\Events\UserTyping;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('rooms', RoomController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/messages/{user}', [DirectMessageController::class, 'show'])
        ->name('messages.direct.show');

    Route::post('/messages/{user}', [DirectMessageController::class, 'store'])
        ->name('messages.direct.store');

    Route::post('/rooms/{room}/members', [RoomController::class, 'addMember'])
        ->name('rooms.members.add');

    Route::delete('/rooms/{room}/members/{user}', [RoomController::class, 'removeMember'])
        ->name('rooms.members.remove');

    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])
        ->name('rooms.destroy');
});

Route::post('/rooms/{room}/messages', [MessageController::class, 'store'])
    ->name('rooms.messages.store');

Route::post('/rooms/{room}/invite', [RoomController::class, 'invite'])
    ->name('rooms.invite');

Route::post('/typing', function (Request $request) {
    broadcast(new UserTyping(
        $request->conversation_id,
        $request->user()
    ));

    return response()->noContent();
})->middleware('auth');
