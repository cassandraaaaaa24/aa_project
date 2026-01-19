<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tweet;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the specified user's profile.
     */
    public function show($id, Request $request)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Build query for user's tweets
        $query = Tweet::where('user_id', $id)->with(['tags', 'likes']);

        // Filter by tag
        if ($request->filled('tag')) {
            $tagName = $request->input('tag');
            $query->whereHas('tags', function($q) use ($tagName) {
                $q->where('name', $tagName);
            });
        }

        // Sort by different criteria
        $sortBy = $request->input('sort', 'date_desc'); // Default to newest first
        
        switch ($sortBy) {
            case 'date_asc':
                $query->oldest(); // Oldest first
                break;
            case 'date_desc':
                $query->latest(); // Newest first
                break;
            case 'likes_asc':
                $query->orderBy('likes_count', 'asc'); // Least liked first
                break;
            case 'likes_desc':
                $query->orderBy('likes_count', 'desc'); // Most liked first
                break;
            default:
                $query->latest(); // Default to newest
        }

        // Get the filtered tweets
        $tweets = $query->get();

        // Load the relationship for the original user object (for stats)
        $user->load(['tweets' => function($q) {
            $q->with('tags');
        }]);

        // Get all tags used by this user for the filter dropdown
        $userTags = Tag::whereHas('tweets', function($q) use ($id) {
            $q->where('user_id', $id);
        })->orderBy('name')->get();

        // Pass user and their tweets to the view
        return view('user', compact('user', 'tweets', 'userTags'));
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

    /**
     * Follow a user.
     */
    public function follow($id)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to follow users.');
        }

        $user = Auth::user();
        
        if ($user->id == $id) {
            return redirect()->back()->with('error', 'You cannot follow yourself.');
        }

        if ($user->follow($id)) {
            return redirect()->back()->with('success', 'You are now following this user!');
        }

        return redirect()->back()->with('info', 'You are already following this user.');
    }

    /**
     * Unfollow a user.
     */
    public function unfollow($id)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in.');
        }

        $user = Auth::user();

        if ($user->unfollow($id)) {
            return redirect()->back()->with('success', 'You have unfollowed this user.');
        }

        return redirect()->back()->with('info', 'You are not following this user.');
    }
}