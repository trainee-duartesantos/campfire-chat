<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    protected $fillable = [
        'name',
        'avatar',
        'created_by',
    ];

    // Quem criou a sala
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //Utilizadores pertencentes à sala
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withTimestamps();
    }

    //Mensagens da sala
    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable')->latest();
    }
}
