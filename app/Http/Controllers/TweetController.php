<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;
use Illuminate\Support\Facades\Auth;

class TweetController extends Controller
{
    /**
     * Show the form for creating a new tweet.
     */
    public function create()
    {
        return view('tweets.edit');
    }

    /**
     * Display a listing of tweets.
     */
    public function index()
    {
        $tweets = Tweet::with('user')
            ->select('id', 'user_id', 'content', 'likes_count', 'created_at', 'updated_at')
            ->latest()
            ->get();

        return view('tweets.index', compact('tweets'));
    }

    /**
     * Show a single tweet.
     */
    public function show($id)
    {
        $tweet = Tweet::with('user')
            ->select('id', 'user_id', 'content', 'likes_count', 'created_at', 'updated_at')
            ->findOrFail($id);

        return view('tweets.show', compact('tweet'));
    }

    /**
     * Show the form for editing a tweet.
     * Only the owner can access this page.
     */
    public function edit($id)
    {
        $tweet = Tweet::findOrFail($id);

        if (Auth::id() !== $tweet->user_id) {
            return redirect()->route('tweets.index')
                ->with('error', 'Unauthorized. You can only edit your own tweets.');
        }

        return view('tweets.edit', compact('tweet'));
    }

    /**
     * Store a newly created tweet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:280',
        ]);

        $tweet = Tweet::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'likes_count' => 0,
        ]);

        return redirect()->route('tweets.index')
            ->with('success', 'Tweet posted successfully!');
    }

    /**
     * Update an existing tweet.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:280',
        ]);

        $tweet = Tweet::findOrFail($id);

        if (Auth::id() !== $tweet->user_id) {
            return redirect()->route('tweets.index')
                ->with('error', 'Unauthorized. You can only update your own tweets.');
        }

        $tweet->update($validated);

        return redirect()->route('tweets.index')
            ->with('success', 'Tweet updated successfully!');
    }

    /**
     * Delete a tweet.
     */
    public function destroy($id)
    {
        $tweet = Tweet::findOrFail($id);

        if (Auth::id() !== $tweet->user_id) {
            return redirect()->route('tweets.index')
                ->with('error', 'Unauthorized. You can only delete your own tweets.');
        }

        $tweet->delete();

        return redirect()->route('tweets.index')
            ->with('success', 'Tweet deleted successfully!');
    }

    /**
     * Like a tweet.
     */
    public function like($id)
    {
        $tweet = Tweet::findOrFail($id);

        // Ensure user is logged in
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to like tweets.');
        }

        $user = Auth::user();

        // Check if this user already liked the tweet
        $alreadyLiked = $tweet->likes()->where('user_id', $user->id)->exists();

        if ($alreadyLiked) {
            // Unlike: remove from pivot table
            $tweet->likes()->detach($user->id);

            // Decrement counter safely (avoid negative values)
            if ($tweet->likes_count > 0) {
                $tweet->decrement('likes_count');
            }

            return redirect()->back()->with('success', 'You unliked this tweet.');
        } else {
            // Like: add to pivot table
            $tweet->likes()->attach($user->id);

            // Increment counter
            $tweet->increment('likes_count');

            return redirect()->back()->with('success', 'Tweet liked successfully!');
        }
    }
    public function unlike($id)
    {
        $tweet = Tweet::findOrFail($id);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to unlike tweets.');
        }

        $user = Auth::user();

        // Check if this user has liked the tweet
        $alreadyLiked = $tweet->likes()->where('user_id', $user->id)->exists();

        if (!$alreadyLiked) {
            return redirect()->back()->with('error', 'You have not liked this tweet yet.');
        }

        // Remove the like
        $tweet->likes()->detach($user->id);

        // Decrement counter safely
        if ($tweet->likes_count > 0) {
            $tweet->decrement('likes_count');
        }

        return redirect()->back()->with('success', 'You unliked this tweet.');
    }

}
