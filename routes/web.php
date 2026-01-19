<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes (GET + POST only, with basic validation)
|--------------------------------------------------------------------------
*/

// Home route → tweets feed
Route::get('/', [TweetController::class, 'index'])->name('tweets.index');

// Show a single tweet
Route::get('/tweets/{id}', [TweetController::class, 'show'])->name('tweets.show');

// Create a new tweet (form page)
Route::get('/tweets/create', [TweetController::class, 'create'])->name('tweets.create');
// Store new tweet
Route::post('/tweets', [TweetController::class, 'store'])->name('tweets.store');

// Edit a tweet (form page)
Route::get('/tweets/{id}/edit', [TweetController::class, 'edit'])->name('tweets.edit');
// Update a tweet (POST instead of PUT)
Route::put('/tweets/{id}/update', [TweetController::class, 'update'])->name('tweets.update');

// Delete a tweet (POST instead of DELETE)
Route::delete('/tweets/{id}/delete', [TweetController::class, 'destroy'])->name('tweets.destroy');

// Like a tweet (POST)
Route::post('/tweets/{id}/like', [TweetController::class, 'like'])->name('tweets.like');

// Unlike a tweet (POST)
Route::post('/tweets/{id}/unlike', [TweetController::class, 'unlike'])->name('tweets.unlike');

// Landing page for guests
Route::get('/landing', function () {
    return view('landing');
})->name('landing');

// Register page (form view)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Register form submission with validation
Route::post('/register', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);

    return redirect()->route('tweets.index')->with('success', 'Registered successfully!');
})->name('register.store');

// Login page (form view)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login form submission with validation
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('tweets.index')->with('success', 'Logged in successfully!');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials provided.',
    ])->onlyInput('email');
})->name('login.store');

// Logout (POST)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing')->with('success', 'Logged out successfully!');
})->name('logout');

// User profile route
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

// Edit profile routes (authenticated only)
Route::get('/profile/edit', [UserController::class, 'editProfile'])->middleware('auth')->name('profile.edit');
Route::post('/profile/update', [UserController::class, 'updateProfile'])->middleware('auth')->name('profile.update');