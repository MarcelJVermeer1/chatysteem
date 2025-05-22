<?php

namespace App\Http\Controllers;

use App\Models\friendships;
use App\Models\User;
use Illuminate\Http\Request;

class FriendshipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::query()
            ->where('id', '!=', auth()->id()) // exclude current user
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->get();

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(friendships $friendships)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(friendships $friendships)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, friendships $friendships)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(friendships $friendships)
    {
        //
    }
}
