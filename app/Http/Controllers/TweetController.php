<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
     * Display a listing of tweets with search and filtering.
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = Tweet::with(['user', 'tags', 'likes'])
            ->select('id', 'user_id', 'content', 'likes_count', 'image', 'created_at', 'updated_at');

        // Search by content
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('content', 'like', '%' . $search . '%');
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $tagName = $request->input('tag');
            $query->whereHas('tags', function($q) use ($tagName) {
                $q->where('name', $tagName);
            });
        }

        // Filter by author
        if ($request->filled('author')) {
            $authorName = $request->input('author');
            $query->whereHas('user', function($q) use ($authorName) {
                $q->where('name', 'like', '%' . $authorName . '%');
            });
        }

        // Sort by date
        $sortOrder = $request->input('sort', 'desc'); // Default to newest first
        if ($sortOrder === 'asc') {
            $query->oldest(); // Oldest first
        } else {
            $query->latest(); // Newest first (default)
        }

        // Get the tweets
        $tweets = $query->get();

        // Get all tags for the filter dropdown
        $allTags = Tag::orderBy('name')->get();

        // Get all users for author search suggestions
        $allUsers = User::select('id', 'name')->orderBy('name')->get();

        return view('tweets.index', compact('tweets', 'allTags', 'allUsers'));
    }

    /**
     * Show a single tweet.
     */
    public function show($id)
    {
        $tweet = Tweet::with(['user', 'tags', 'likes'])
            ->select('id', 'user_id', 'content', 'likes_count', 'image', 'created_at', 'updated_at')
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
        // Validate without MIME type checking if fileinfo extension is not available
        $rules = [
            'content' => 'required|string|max:280',
            'tags' => 'nullable|string',
        ];

        // Only add image validation if file is present
        if ($request->hasFile('image')) {
            $rules['image'] = 'file|max:2048';
        }

        $validated = $request->validate($rules);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Manual validation for image types
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid image format. Allowed: JPEG, PNG, GIF, WEBP');
            }
            
            $imagePath = $file->store('tweets', 'public');
        }

        $tweet = Tweet::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'image' => $imagePath,
            'likes_count' => 0,
        ]);

        // Handle tags
        if (!empty($validated['tags'])) {
            $tagNames = array_map('trim', explode(',', $validated['tags']));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            if (!empty($tagIds)) {
                $tweet->tags()->attach($tagIds);
            }
        }

        return redirect()->route('tweets.index')
            ->with('success', 'Tweet posted successfully!');
    }

    /**
     * Update an existing tweet.
     */
    public function update(Request $request, $id)
    {
        // Validate without MIME type checking if fileinfo extension is not available
        $rules = [
            'content' => 'required|string|max:280',
            'tags' => 'nullable|string',
        ];

        // Only add image validation if file is present
        if ($request->hasFile('image')) {
            $rules['image'] = 'file|max:2048';
        }

        $validated = $request->validate($rules);

        $tweet = Tweet::findOrFail($id);

        if (Auth::id() !== $tweet->user_id) {
            return redirect()->route('tweets.index')
                ->with('error', 'Unauthorized. You can only update your own tweets.');
        }

        $tweet->content = $validated['content'];

        // Handle image update
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Manual validation for image types
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid image format. Allowed: JPEG, PNG, GIF, WEBP');
            }
            
            if ($tweet->image) {
                Storage::disk('public')->delete($tweet->image);
            }
            $tweet->image = $file->store('tweets', 'public');
        }

        $tweet->save();

        // Handle tags update
        $tweet->tags()->detach();
        if (!empty($validated['tags'])) {
            $tagNames = array_map('trim', explode(',', $validated['tags']));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            if (!empty($tagIds)) {
                $tweet->tags()->attach($tagIds);
            }
        }

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