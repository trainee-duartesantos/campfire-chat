<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'body',
    ];

    /**
     * Autor da mensagem
     */
    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable')->latest();
    }

    /**
     * Destino da mensagem (Room ou User)
     */
    public function messageable(): MorphTo
    {
        return $this->morphTo();
    }
}
