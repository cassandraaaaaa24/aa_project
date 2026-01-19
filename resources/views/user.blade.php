@extends('layouts.app')

@section('content')
<div class="card profile-card">
    <!-- Profile Picture -->
    @if($user->profile_picture)
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" 
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
        </div>
    @endif

    <!-- User Info -->
    <h2 class="profile-name">{{ $user->name }}</h2>
    
    <!-- Biography -->
    @if($user->bio)
        <p style="margin: 10px 0; color: #666; font-size: 14px;">{{ $user->bio }}</p>
    @endif
    
    <p class="profile-joined">Joined: {{ $user->created_at->format('F j, Y') }}</p>

    <!-- Edit Button (only for own profile) -->
    @auth
        @if(auth()->id() === $user->id)
            <a href="{{ route('profile.edit') }}" class="btn btn-primary" style="margin: 10px 0;">Edit Profile</a>
        @endif
    @endauth

    <!-- Stats -->
    <div class="profile-stats">
        <span>Total Tweets: {{ $user->tweets->count() }}</span>
        <span>Total Likes: {{ $user->tweets->sum('likes_count') }}</span>
    </div>
</div>

<!-- User Tweets -->
<div class="tweets-list">
    <h3>{{ $user->name }}'s Tweets</h3>

    @forelse($user->tweets as $tweet)
        <div class="card tweet-card">
            <p>{{ $tweet->content }}</p>
            
            @if($tweet->image)
                <div style="margin: 10px 0;">
                    <img src="{{ Storage::url($tweet->image) }}" alt="Tweet image" style="max-width: 100%; border-radius: 8px;">
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
                <span>{{ $tweet->created_at->diffForHumans() }}</span>
                <span>Likes: {{ $tweet->likes_count }}</span>
            </div>
        </div>
    @empty
        <p>No tweets yet.</p>
    @endforelse
</div>
@endsection
