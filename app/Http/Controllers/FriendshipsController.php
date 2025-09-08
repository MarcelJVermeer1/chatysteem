<?php

namespace App\Http\Controllers;

use App\Models\Friendships;
use App\Models\User;
use App\Models\Messages;
use Illuminate\Http\Request;

class FriendshipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->query('search');
        $authId = auth()->id();

        $users = User::query()
            ->where('id', '!=', $authId)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->get()
            ->filter(function ($user) use ($authId) {
                //Filter out accepted friendships in both directions
                return !Friendships::where(function ($query) use ($authId, $user) {
                    $query->where('sender_id', $authId)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($authId, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $authId);
                })->where('status', 'accepted')->exists();
            })
            ->map(function ($user) use ($authId) {
                // Mark as pending if there's a pending request from logged-in user to that user
                $user->pending = Friendships::where('sender_id', $authId)
                    ->where('receiver_id', $user->id)
                    ->where('status', 'pending')
                    ->exists();

                return $user;
            })
            ->values(); // reset collection keys

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(User $receiver)
    {
        $sender = auth()->user();

        if($sender->id === $receiver->id){
            return back()->withErrors("You cannot add yourself as a friend");
        }

        $alreadyExists = Friendships::where(function ($query) use ($sender, $receiver){
            $query->where('sender_id', $sender->id)
                ->where('receiver_id' , $receiver->id);
        })->orwhere(function ($query) use ($sender , $receiver) {
                $query->where('sender_id', $receiver->id)
                    ->where('receiver_id',$sender->id);
        })->exists();


        if($alreadyExists){
            return back()->withErrors('Friend request already exists or you are already friends.');
        }

        friendships::create([
            'sender_id'=>$sender->id,
            'receiver_id'=> $receiver->id,
            'status'=>'pending',
        ]);

        return back()->with('success', 'Friend request sent!');

    }

    /**
     * Display the specified resource.
     */
    public function show(friendships $friendships)
    {
        $userId = auth()->id();

        $pendingRequests = Friendships::with('sender')
        ->where('receiver_id', $userId)
            ->where('status', 'pending')
            ->get();

        return view('friendships.requests', compact('pendingRequests'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(friendships $friendships)
    {
        //
    }
    public function accept($id)
    {
        $friendship = Friendships::where('id', $id)
            ->where('receiver_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Friend request accepted!');

    }

    public function deny($id)
    {
        $friendship = Friendships::where('id', $id)
            ->where('receiver_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->delete();

        return back()->with('success', 'Friend request denied.');

    }


}
