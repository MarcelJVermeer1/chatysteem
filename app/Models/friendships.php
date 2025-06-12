<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Friendships extends Model
{
    protected $table = 'friendships';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
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
