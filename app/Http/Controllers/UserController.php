<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the specified user's profile.
     */
    public function show($id)
    {
        // Find the user by ID, or fail if not found
        $user = User::with('tweets')->findOrFail($id);

        // Pass user and their tweets to the view
        return view('user', compact('user'));
    }
}
