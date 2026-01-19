@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\DB;
@endphp

<div class="card profile-card">
    <!-- Profile Picture -->
    @if($user->profile_picture)
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                 alt="Profile" 
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;"
                 onerror="this.style.display='none';">
        </div>
    @endif

    <!-- User Info -->
    <h2 class="profile-name">{{ $user->name }}</h2>
    
    <!-- Biography -->
    @if($user->bio)
        <p style="margin: 10px 0; color: #666; font-size: 14px;">{{ $user->bio }}</p>
    @endif
    
    <p class="profile-joined">Joined: {{ $user->created_at->format('F j, Y') }}</p>

    <!-- Edit Button or Follow Button -->
    @auth
        @if(auth()->id() === $user->id)
            <!-- Edit Profile Button (for own profile) -->
            <a href="{{ route('profile.edit') }}" class="btn btn-primary" style="margin: 10px 0;">Edit Profile</a>
        @else
            <!-- Follow/Unfollow Button (for other users) -->
            @php
                // Check if following using database query to avoid method error
                $isFollowing = false;
                try {
                    $isFollowing = DB::table('follows')
                        ->where('follower_id', auth()->id())
                        ->where('following_id', $user->id)
                        ->exists();
                } catch (\Exception $e) {
                    // follows table doesn't exist yet
                }
            @endphp

            @if($isFollowing)
                <form action="{{ route('users.unfollow', $user->id) }}" method="POST" style="display: inline-block; margin: 10px 0;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        ✓ Following
                    </button>
                </form>
            @else
                <form action="{{ route('users.follow', $user->id) }}" method="POST" style="display: inline-block; margin: 10px 0;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        + Follow
                    </button>
                </form>
            @endif
        @endif
    @endauth
        
    <!-- Stats -->
    <div class="profile-stats">
        <span>Total Yaps: {{ $user->tweets->count() }}</span>
        <span>Total Likes: {{ $user->tweets->sum('likes_count') }}</span>
        @php
            // Get follower/following counts safely
            $followersCount = 0;
            $followingCount = 0;
            try {
                $followersCount = DB::table('follows')->where('following_id', $user->id)->count();
                $followingCount = DB::table('follows')->where('follower_id', $user->id)->count();
            } catch (\Exception $e) {
                // follows table doesn't exist yet
            }
        @endphp
        <span>Followers: {{ $followersCount }}</span>
        <span>Following: {{ $followingCount }}</span>
    </div>
</div>

<!-- User Tweets Section -->
<div class="tweets-list">
    <h3>{{ $user->name }}'s Yaps</h3>

    <!-- Filter and Sort Section -->
    @if($user->tweets->count() > 0)
        <div class="card mb-4" style="background-color: #f8f9fa; padding: 15px;">
            <form method="GET" action="{{ route('users.show', $user->id) }}">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    
                    <!-- Filter by Tag -->
                    <div>
                        <label for="tag" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                            Filter by Tag
                        </label>
                        <select id="tag" name="tag" class="input-field">
                            <option value="">All Tags</option>
                            @foreach($userTags as $tag)
                                <option value="{{ $tag->name }}" {{ request('tag') == $tag->name ? 'selected' : '' }}>
                                    #{{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div>
                        <label for="sort" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                            Sort By
                        </label>
                        <select id="sort" name="sort" class="input-field">
                            <option value="date_desc" {{ request('sort', 'date_desc') == 'date_desc' ? 'selected' : '' }}>
                                Date: Newest First
                            </option>
                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>
                                Date: Oldest First
                            </option>
                            <option value="likes_desc" {{ request('sort') == 'likes_desc' ? 'selected' : '' }}>
                                Likes: Most First
                            </option>
                            <option value="likes_asc" {{ request('sort') == 'likes_asc' ? 'selected' : '' }}>
                                Likes: Least First
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        Apply Filters
                    </button>
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                        Clear
                    </a>
                </div>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['tag', 'sort']))
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                        <strong style="font-size: 14px;">Active:</strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                            @if(request('tag'))
                                <span style="background-color: #6f42c1; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                                    Tag: #{{ request('tag') }}
                                </span>
                            @endif
                            @if(request('sort') && request('sort') != 'date_desc')
                                <span style="background-color: #17a2b8; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                                    @if(request('sort') == 'date_asc')
                                        Oldest First
                                    @elseif(request('sort') == 'likes_desc')
                                        Most Liked
                                    @elseif(request('sort') == 'likes_asc')
                                        Least Liked
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Results Count -->
        <div style="margin-bottom: 15px; color: #666; font-size: 14px;">
            <strong>{{ $tweets->count() }}</strong> yap(s) shown
            @if(request('tag'))
                with tag <strong>#{{ request('tag') }}</strong>
            @endif
        </div>
    @endif

    <!-- Tweets List -->
    @forelse($tweets as $tweet)
        <div class="card tweet-card">
            <!-- Tweet Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="color: #666; font-size: 14px;">
                    {{ $tweet->created_at->diffForHumans() }}
                </span>
                <span style="color: #666; font-size: 14px;">
                    ❤️ {{ $tweet->likes_count }} {{ $tweet->likes_count == 1 ? 'like' : 'likes' }}
                </span>
            </div>

            <!-- Content -->
            <p style="margin-bottom: 10px;">{{ $tweet->content }}</p>
            
            <!-- Image -->
            @if($tweet->image)
                <div style="margin: 10px 0;">
                    <img src="{{ asset('storage/' . $tweet->image) }}" 
                         alt="Yap image" 
                         style="max-width: 100%; border-radius: 8px;"
                         onerror="this.style.display='none';">
                </div>
            @endif

            <!-- Tags -->
            @if($tweet->tags->count() > 0)
                <div style="margin: 10px 0;">
                    @foreach($tweet->tags as $tag)
                        <a href="{{ route('users.show', ['id' => $user->id, 'tag' => $tag->name]) }}" 
                           style="background-color: #e0e7ff; color: #4c51bf; padding: 4px 8px; border-radius: 12px; margin-right: 5px; font-size: 12px; text-decoration: none; display: inline-block;">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="tweet-actions" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                @auth
                    @if(auth()->id() === $tweet->user_id)
                        <a href="{{ route('tweets.edit', $tweet->id) }}" class="btn-link">Edit</a>
                        <form action="{{ route('tweets.destroy', $tweet->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-link btn-danger" onclick="return confirm('Are you sure you want to delete this yap?')">Delete</button>
                        </form>
                    @endif
                @endauth

                <a href="{{ route('tweets.show', $tweet->id) }}" class="btn-link">View Details</a>
            </div>
        </div>
    @empty
        <div class="card" style="text-align: center; padding: 40px;">
            <p class="no-tweets">
                @if(request('tag'))
                    No yaps found with tag #{{ request('tag') }}. Try a different tag.
                @else
                    No yaps yet.
                @endif
            </p>
        </div>
    @endforelse
</div>

<style>
    .input-field {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }

    .input-field:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    select.input-field {
        cursor: pointer;
    }

    .tweet-card {
        margin-bottom: 20px;
    }

    /* Follow Button Styles */
    .btn-primary {
        background-color: #007bff;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection