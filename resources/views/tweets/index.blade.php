@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="feed-title">Tweets Feed</h2>

    @auth
    <div class="card mb-6">
        <form method="POST" action="{{ route('tweets.store') }}">
            @csrf
            <textarea name="content" rows="3" placeholder="What's happening?" class="textarea" required></textarea>
            @error('content')
                <span class="error">{{ $message }}</span>
            @enderror
            <button type="submit" class="btn btn-primary">Tweet</button>
        </form>
    </div>
    @endauth

    @forelse($tweets as $tweet)
        <div class="card mb-4">
            <div class="tweet-header">
                <a href="{{ route('users.show', $tweet->user->id) }}" class="tweet-user">
                    {{ $tweet->user->name ?? 'Unknown User' }}
                </a>
                <span class="tweet-time">{{ $tweet->created_at->diffForHumans() }}</span>
            </div>
            <p class="tweet-content">{{ $tweet->content }}</p>

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
        <p class="no-tweets">No tweets yet. Be the first to post!</p>
    @endforelse
</div>
@endsection
