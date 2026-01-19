<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Show edit profile form.
     */
    public function editProfile()
    {
        return view('auth.edit-profile', ['user' => Auth::user()]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $validated['name'];
        $user->bio = $validated['bio'];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->save();

        return redirect()->route('user.show', $user->id)
            ->with('success', 'Profile updated successfully!');
    }
}
