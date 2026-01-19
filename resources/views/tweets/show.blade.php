@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Tweet</h2>
        <span class="text-sm text-gray-500">{{ $tweet->created_at->diffForHumans() }}</span>
    </div>

    <!-- Tweet Content -->
    <p class="mb-4">{{ $tweet->content }}</p>

    <!-- Author -->
    <p class="text-sm text-gray-600 mb-4">
        Posted by <span class="font-semibold">{{ $tweet->user->name ?? 'Unknown User' }}</span>
    </p>

    <!-- Actions -->
    <div class="tweet-actions">
    @auth
        @if(auth()->id() === $tweet->user_id)
            <!-- Edit -->
            <a href="{{ route('tweets.edit', $tweet->id) }}" class="btn-link">Edit</a>
            
            <!-- Delete -->
            <form action="{{ route('tweets.destroy', $tweet->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-link btn-danger">Delete</button>
            </form>
        @endif
    @endauth

    <!-- Like / Unlike -->
    @auth
        @php
            $liked = $tweet->likes()->where('user_id', auth()->id())->exists();
        @endphp

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
@endsection
