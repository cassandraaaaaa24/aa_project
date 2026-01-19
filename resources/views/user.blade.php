@extends('layouts.app')

@section('content')
<div class="card profile-card">
    <!-- User Info -->
    <h2 class="profile-name">{{ $user->name }}</h2>
    <p class="profile-joined">Joined: {{ $user->created_at->format('F j, Y') }}</p>

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
