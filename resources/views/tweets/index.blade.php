@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="feed-title">Yap Feed</h2>

    <!-- Search and Filter Section -->
    <div class="card mb-4" style="background-color: #f8f9fa; padding: 20px;">
        <h3 style="margin-bottom: 15px; font-size: 18px; font-weight: bold;">Search & Filter</h3>
        
        <form method="GET" action="{{ route('tweets.index') }}" id="filterForm">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                
                <!-- Search by Content -->
                <div>
                    <label for="search" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                        Search Content
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search tweets..." 
                           class="input-field">
                </div>

                <!-- Filter by Author -->
                <div>
                    <label for="author" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                        Author
                    </label>
                    <input type="text" 
                           id="author" 
                           name="author" 
                           value="{{ request('author') }}"
                           placeholder="Search by author name..." 
                           class="input-field"
                           list="authorList">
                    <datalist id="authorList">
                        @foreach($allUsers as $user)
                            <option value="{{ $user->name }}">
                        @endforeach
                    </datalist>
                </div>

                <!-- Filter by Tag -->
                <div>
                    <label for="tag" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                        Filter by Tag
                    </label>
                    <select id="tag" name="tag" class="input-field">
                        <option value="">All Tags</option>
                        @foreach($allTags as $tag)
                            <option value="{{ $tag->name }}" {{ request('tag') == $tag->name ? 'selected' : '' }}>
                                #{{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort by Date -->
                <div>
                    <label for="sort" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                        Sort by Date
                    </label>
                    <select id="sort" name="sort" class="input-field">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>
                            Newest First
                        </option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>
                            Oldest First
                        </option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
                <a href="{{ route('tweets.index') }}" class="btn btn-secondary">
                    Clear All
                </a>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['search', 'tag', 'author', 'sort']))
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <strong style="font-size: 14px;">Active Filters:</strong>
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                    @if(request('search'))
                        <span style="background-color: #007bff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                            Content: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('author'))
                        <span style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                            Author: {{ request('author') }}
                        </span>
                    @endif
                    @if(request('tag'))
                        <span style="background-color: #6f42c1; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                            Tag: #{{ request('tag') }}
                        </span>
                    @endif
                    @if(request('sort') == 'asc')
                        <span style="background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                            Sort: Oldest First
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Tweet Creation Form -->
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

    <!-- Results Count -->
    <div style="margin-bottom: 15px; color: #666; font-size: 14px;">
        <strong>{{ $tweets->count() }}</strong> tweet(s) found
    </div>

    <!-- Tweets List -->
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
                        <a href="{{ route('tweets.index', ['tag' => $tag->name]) }}" 
                           style="background-color: #e0e7ff; color: #4c51bf; padding: 4px 8px; border-radius: 12px; margin-right: 5px; font-size: 12px; text-decoration: none; display: inline-block;">
                            #{{ $tag->name }}
                        </a>
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
        <div class="card" style="text-align: center; padding: 40px;">
            <p class="no-tweets">
                @if(request()->hasAny(['search', 'tag', 'author']))
                    No yaps found matching your filters. Try adjusting your search criteria.
                @else
                    No yaps yet. Be the first to post!
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

    @media (max-width: 768px) {
        .card > form > div[style*="grid"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection