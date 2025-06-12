<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function friends()
    {
        return User::whereHas('sentFriendships', function ($query) {
            $query->where('status', 'accepted')
                ->where('receiver_id', $this->id);
        })->orWhereHas('receivedFriendships', function ($query) {
            $query->where('status', 'accepted')
                ->where('sender_id', $this->id);
        })->get();
    }

    public function sentFriendships()
    {
        return $this->hasMany(\App\Models\Friendships::class, 'sender_id');
    }

    public function receivedFriendships()
    {
        return $this->hasMany(\App\Models\Friendships::class, 'receiver_id');
    }
}
