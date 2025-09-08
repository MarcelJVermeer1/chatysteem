<?php

namespace App\Http\Controllers;

use App\Models\Friendships;
use App\Models\messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $friends = $this->getAcceptedFriends($user->id);

        return view('dashboard', [
            'friends' => $friends,
            'receiver' => null,
            'messages' => [],
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,User $user)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        Messages::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'body' => $request->input('body'),
            'read_at' => null,
        ]);

        return redirect()->route('messages.with', $user->id);
    }

    // ✅ Helper-functie om geaccepteerde vrienden op te halen
    private function getAcceptedFriends($userId)
    {
        $friendIds = Friendships::where(function ($query) use ($userId) {
            $query->where('status', 'accepted')
                ->where('sender_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('status', 'accepted')
                ->where('receiver_id', $userId);
        })->get()->map(function ($friendship) use ($userId) {
            return $friendship->sender_id === $userId
                ? $friendship->receiver_id
                : $friendship->sender_id;
        });

        return User::whereIn('id', $friendIds)->get()->map(function ($friend) use ($userId) {
            $friend->hasUnread = Messages::where('sender_id', $friend->id)
                ->where('receih ver_id', $userId)
                ->whereNull('read_at')
                ->exists();
            return $friend;
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(messages $messages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(messages $messages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, messages $messages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(messages $messages)
    {
        //
    }

    public function with(User $user)
    {
        $authId = Auth::id();

        $messages = Messages::where(function ($query) use ($authId, $user) {
            $query->where('sender_id', $authId)
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($authId, $user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $authId);
        })->orderBy('created_at')->get();

        Messages::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $friends = $this->getAcceptedFriends($authId);

        return view('dashboard', [
            'friends' => $friends,
            'receiver' => $user,
            'messages' => $messages,
        ]);
    }

}
