@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="feed-title">Yap Feed</h2>

    @auth
    <div class="card mb-6">
        <form method="POST" action="{{ route('tweets.store') }}" enctype="multipart/form-data">
            @csrf
            <textarea name="content" rows="3" placeholder="What's happening?" class="textarea" required></textarea>
            @error('content')
                <span class="error">{{ $message }}</span>
            @enderror
            
            <div style="margin-top: 10px;">
                <input type="file" name="image" accept="image/*" class="input-field" style="display: block; margin-bottom: 10px;">
                <input type="text" name="tags" placeholder="Tags (comma-separated)" class="input-field">
            </div>
            
            <button type="submit" class="btn btn-primary">Tweet</button>
        </form>
    </div>
    @endauth

    @forelse($tweets as $tweet)
        <div class="card mb-4">
            <div class="tweet-header">
                <a href="{{ route('users.show', $tweet->user->id) }}" class="tweet-user">
                    @if($tweet->user->profile_picture)
                        <img src="{{ asset('storage/' . $tweet->user->profile_picture) }}" 
                             alt="Profile" 
                             style="width: 40px; height: 40px; border-radius: 50%; display: inline-block; margin-right: 10px; object-fit: cover;"
                             onerror="this.style.display='none';">
                    @endif
                    {{ $tweet->user->name ?? 'Unknown User' }}
                </a>
                <span class="tweet-time">{{ $tweet->created_at->diffForHumans() }}</span>
            </div>
            <p class="tweet-content">{{ $tweet->content }}</p>

            @if($tweet->image)
                <div style="margin: 10px 0;">
                    <img src="{{ asset('storage/' . $tweet->image) }}" 
                         alt="Tweet image" 
                         style="max-width: 100%; border-radius: 8px; display: block;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <p style="display: none; color: #666; font-size: 12px;">Image could not be loaded: {{ $tweet->image }}</p>
                </div>
            @endif

            @if($tweet->tags->count() > 0)
                <div style="margin: 10px 0;">
                    @foreach($tweet->tags as $tag)
                        <span style="background-color: #e0e7ff; color: #4c51bf; padding: 4px 8px; border-radius: 12px; margin-right: 5px; font-size: 12px;">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="tweet-actions">
                @auth
                    @if(auth()->id() === $tweet->user_id)
                        <a href="{{ route('tweets.edit', $tweet->id) }}" class="btn-link">Edit</a>
                        <form action="{{ route('tweets.destroy', $tweet->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-link btn-danger">Delete</button>
                        </form>
                    @endif
                @endauth

                @auth
                    @php $liked = $tweet->likes()->where('user_id', auth()->id())->exists(); @endphp
                    @if($liked)
                        <form action="{{ route('tweets.unlike', $tweet->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-link btn-success">
                                Unlike ({{ $tweet->likes_count ?? 0 }})
                            </button>
                        </form>
                    @else
                        <form action="{{ route('tweets.like', $tweet->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-link btn-success">
                                Like ({{ $tweet->likes_count ?? 0 }})
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <p class="no-tweets">No yaps yet. Be the first to post!</p>
    @endforelse
</div>
@endsection