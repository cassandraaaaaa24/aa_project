<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment.
     */
    public function store(Request $request, $tweetId)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to comment.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $tweet = Tweet::findOrFail($tweetId);

        Comment::create([
            'user_id' => Auth::id(),
            'tweet_id' => $tweet->id,
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Comment posted successfully!');
    }

    /**
     * Delete a comment.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Check if user owns the comment or owns the tweet
        if (Auth::id() !== $comment->user_id && Auth::id() !== $comment->tweet->user_id) {
            return redirect()->back()->with('error', 'Unauthorized. You can only delete your own comments.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}