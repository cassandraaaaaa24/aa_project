@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">{{ isset($tweet) ? 'Edit Tweet' : 'New Tweet' }}</h2>

    <!-- Tweet Form -->
    <form method="POST" action="{{ isset($tweet) ? route('tweets.update', $tweet->id) : route('tweets.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($tweet))
            @method('PUT')
        @endif

        <!-- Content -->
        <div class="mb-4">
            <label for="content" class="block text-sm font-medium">Tweet</label>
            <textarea id="content" name="content" rows="3" required
                      class="w-full border rounded px-3 py-2">{{ old('content', isset($tweet) ? $tweet->content : '') }}</textarea>
            @error('content')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Image Upload -->
        <div class="mb-4">
            <label for="image" class="block text-sm font-medium">Upload Image</label>
            <input type="file" id="image" name="image" accept="image/*"
                   class="w-full border rounded px-3 py-2">
            @error('image')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            @if(isset($tweet) && $tweet->image)
                <p class="text-sm text-gray-500 mt-2">Current image: <img src="{{ Storage::url($tweet->image) }}" class="w-32 mt-2"></p>
            @endif
        </div>

        <!-- Tags -->
        <div class="mb-4">
            <label for="tags" class="block text-sm font-medium">Tags (comma-separated)</label>
            <input type="text" id="tags" name="tags" placeholder="e.g. news, tech, fun"
                   value="{{ old('tags', isset($tweet) ? $tweet->tags->pluck('name')->join(', ') : '') }}"
                   class="w-full border rounded px-3 py-2">
            @error('tags')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                {{ isset($tweet) ? 'Update' : 'Post' }}
            </button>
            <a href="{{ route('tweets.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
