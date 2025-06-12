<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FriendshipsController;
use App\Http\Controllers\MessagesController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessagesController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessagesController::class, 'with'])->name('messages.with');
    Route::post('/messages/{user}', [MessagesController::class, 'store'])->name('messages.store');
});


Route::get('/dashboard',[MessagesController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/avatar/{filename}', function ($filename) {
    if (!Auth::check()) {
        abort(403);
    }

    $path = 'avatars/' . $filename;

    // Optional fallback for broken avatar reference
    if (!Storage::exists($path)) {
        $path = 'avatars/placeholder3.png';

        if (!Storage::exists($path)) {
            abort(404);
        }
    }

    return Response::make(Storage::get($path), 200, [
        'Content-Type' => Storage::mimeType($path),
    ]);
});


Route::get('/users', [FriendshipsController::class, 'index'])->name('users.index');
Route::post('/friendships/{receiver}', [FriendshipsController::class, 'store'])->name('friendships.store');
Route::get('/friend-requests', [FriendshipsController::class, 'show'])->name('friendships.requests');
Route::post('/friendships/{id}/accept', [FriendshipsController::class, 'accept'])->name('friendships.accept');
Route::delete('/friendships/{id}/deny', [FriendshipsController::class, 'deny'])->name('friendships.deny');



require __DIR__.'/auth.php';
