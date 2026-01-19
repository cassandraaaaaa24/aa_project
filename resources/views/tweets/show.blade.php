@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto;">
    <!-- Back Button -->
    <a href="{{ route('tweets.index') }}" style="display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none;">
        ← Back to Feed
    </a>

    <!-- Tweet Card -->
    <div class="card" style="padding: 24px; margin-bottom: 30px;">
        <!-- Tweet Header -->
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('users.show', $tweet->user->id) }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #333;">
                    @if($tweet->user->profile_picture)
                        <img src="{{ asset('storage/' . $tweet->user->profile_picture) }}" 
                             alt="Profile" 
                             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"
                             onerror="this.style.display='none';">
                    @endif
                    <div>
                        <strong style="font-size: 16px;">{{ $tweet->user->name ?? 'Unknown User' }}</strong>
                        <p style="margin: 0; color: #666; font-size: 14px;">{{ $tweet->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Tweet Content -->
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 15px;">{{ $tweet->content }}</p>

        <!-- Tweet Image -->
        @if($tweet->image)
            <div style="margin: 15px 0;">
                <img src="{{ asset('storage/' . $tweet->image) }}" 
                     alt="Tweet image" 
                     style="max-width: 100%; border-radius: 12px; display: block;"
                     onerror="this.style.display='none';">
            </div>
        @endif

        <!-- Tags -->
        @if($tweet->tags && $tweet->tags->count() > 0)
            <div style="margin: 15px 0;">
                @foreach($tweet->tags as $tag)
                    <a href="{{ route('tweets.index', ['tag' => $tag->name]) }}" 
                       style="background-color: #e0e7ff; color: #4c51bf; padding: 6px 12px; border-radius: 15px; margin-right: 8px; font-size: 13px; text-decoration: none; display: inline-block; margin-bottom: 5px;">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Tweet Stats -->
        <div style="display: flex; gap: 20px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; color: #666; font-size: 14px;">
            <span>💬 {{ $tweet->comments()->count() }} {{ $tweet->comments()->count() == 1 ? 'Comment' : 'Comments' }}</span>
            <span>❤️ {{ $tweet->likes_count ?? 0 }} {{ $tweet->likes_count == 1 ? 'Like' : 'Likes' }}</span>
        </div>

        <!-- Actions -->
        <div class="tweet-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; flex-wrap: wrap;">
            @auth
                @if(auth()->id() === $tweet->user_id)
                    <!-- Edit -->
                    <a href="{{ route('tweets.edit', $tweet->id) }}" class="btn-link" style="text-decoration: none; color: #007bff; padding: 8px 16px; border: 1px solid #007bff; border-radius: 6px; font-size: 14px;">Edit</a>
                    
                    <!-- Delete -->
                    <form action="{{ route('tweets.destroy', $tweet->id) }}" method="POST" class="inline" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-link btn-danger" onclick="return confirm('Are you sure you want to delete this yap?')" style="background-color: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">Delete</button>
                    </form>
                @endif

                <!-- Like / Unlike -->
                @php
                    $liked = $tweet->likes()->where('user_id', auth()->id())->exists();
                @endphp

                @if($liked)
                    <form action="{{ route('tweets.unlike', $tweet->id) }}" method="POST" class="inline" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-link" style="background-color: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            ❤️ Unlike
                        </button>
                    </form>
                @else
                    <form action="{{ route('tweets.like', $tweet->id) }}" method="POST" class="inline" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-link" style="background-color: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            🤍 Like
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>

    <!-- Comments Section -->
    <div class="card" style="padding: 24px;">
        <h3 style="margin-bottom: 20px; font-size: 20px; font-weight: 600;">
            Comments ({{ $tweet->comments()->count() }})
        </h3>

        <!-- Add Comment Form -->
        @auth
            <div style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee;">
                <form method="POST" action="{{ route('comments.store', $tweet->id) }}">
                    @csrf
                    <div style="display: flex; gap: 12px; align-items: start;">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                 alt="Your profile" 
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;"
                                 onerror="this.style.display='none';">
                        @endif
                        <div style="flex-grow: 1;">
                            <textarea name="content" 
                                      rows="3" 
                                      placeholder="Write a comment..." 
                                      class="textarea" 
                                      style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 8px; font-size: 14px; resize: vertical; font-family: inherit;"
                                      required></textarea>
                            @error('content')
                                <span style="color: #dc3545; font-size: 13px; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                            <button type="submit" 
                                    class="btn btn-primary" 
                                    style="margin-top: 10px; background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                                Post Comment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 30px;">
                <p style="margin: 0; color: #666;">
                    <a href="{{ route('login') }}" style="color: #007bff; text-decoration: none; font-weight: 600;">Log in</a> to post a comment
                </p>
            </div>
        @endauth

        <!-- Comments List -->
        <div class="comments-list">
            @forelse($tweet->comments as $comment)
                <div class="comment" style="padding: 16px 0; border-bottom: 1px solid #eee;">
                    <div style="display: flex; gap: 12px; align-items: start;">
                        <!-- Commenter Profile Picture -->
                        <a href="{{ route('users.show', $comment->user->id) }}" style="flex-shrink: 0;">
                            @if($comment->user->profile_picture)
                                <img src="{{ asset('storage/' . $comment->user->profile_picture) }}" 
                                     alt="{{ $comment->user->name }}" 
                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                                     onerror="this.style.display='none';">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #007bff; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px;">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>

                        <!-- Comment Content -->
                        <div style="flex-grow: 1;">
                            <div style="margin-bottom: 5px;">
                                <a href="{{ route('users.show', $comment->user->id) }}" style="text-decoration: none; color: #333;">
                                    <strong style="font-size: 15px;">{{ $comment->user->name }}</strong>
                                </a>
                                <span style="color: #999; font-size: 13px; margin-left: 8px;">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p style="margin: 8px 0; color: #333; font-size: 14px; line-height: 1.5;">
                                {{ $comment->content }}
                            </p>

                            <!-- Delete Comment (if owner or tweet owner) -->
                            @auth
                                @if(auth()->id() === $comment->user_id || auth()->id() === $tweet->user_id)
                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="margin-top: 8px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this comment?')"
                                                style="background: none; border: none; color: #dc3545; font-size: 13px; cursor: pointer; padding: 0; text-decoration: underline;">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p style="margin: 0; font-size: 15px;">No comments yet. Be the first to comment!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .comment:last-child {
        border-bottom: none;
    }

    .btn-primary:hover {
        background-color: #0056b3 !important;
    }

    .btn-link:hover {
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }

        .card {
            padding: 16px !important;
        }
    }
</style>
@endsection