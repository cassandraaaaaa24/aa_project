<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the specified user's profile.
     */
    public function show($id)
    {
        // Find the user by ID with tweets and tags, or fail if not found
        $user = User::with(['tweets' => function($query) {
            $query->with('tags')->latest();
        }])->findOrFail($id);

        // Pass user and their tweets to the view
        // Using 'user' since the view is at resources/views/user.blade.php
        return view('user', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function editProfile()
    {
        $user = Auth::user();
        // Using 'auth.edit-profile' since it's at resources/views/auth/edit-profile.blade.php
        return view('auth.edit-profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];

        // Only add profile picture validation if file is present
        if ($request->hasFile('profile_picture')) {
            // Use 'file' instead of 'image' to avoid MIME type issues
            $rules['profile_picture'] = 'file|max:2048';
        }

        // Add password validation only if provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            
            // Manual validation for image types (to avoid MIME type issues)
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid image format. Allowed: JPEG, PNG, GIF, WEBP');
            }
            
            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $user->profile_picture = $file->store('profiles', 'public');
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Profile updated successfully!');
    }
}