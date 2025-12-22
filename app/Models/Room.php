<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Room extends Model
{
    protected $fillable = [
        'name',
        'avatar',
    ];

    /**
     * Utilizadores pertencentes à sala
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withTimestamps();
    }

    /**
     * Mensagens da sala
     */
    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'messageable')
                    ->latest();
    }
}
