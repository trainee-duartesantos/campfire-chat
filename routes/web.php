<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;

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
});

Route::post('/rooms/{room}/messages', [MessageController::class, 'store'])
    ->name('rooms.messages.store');