<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FriendshipsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/avatar/{filename}', function ($filename) {
    $path = 'avatars/' . $filename;

    // Optional: protect access (e.g. only allow logged-in users)
    if (!Auth::check()) {
        abort(403);
    }

    if (!Storage::exists($path)) {
        abort(404);
    }

    return Response::make(Storage::get($path), 200, [
        'Content-Type' => Storage::mimeType($path),
    ]);
});


Route::get('/users', [FriendshipsController::class, 'index'])->name('users.index');



require __DIR__.'/auth.php';
