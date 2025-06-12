<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Messages extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'read_at',
    ];

    // Sender relationship
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Receiver relationship
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
