@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Tweet</h2>

    <!-- Edit Tweet Form -->
    <form method="POST" action="{{ route('tweets.update', $tweet->id) }}">
        @csrf
        @method('PUT')

        <!-- Content -->
        <div class="mb-4">
            <label for="content" class="block text-sm font-medium">Tweet</label>
            <textarea id="content" name="content" rows="3" required
                      class="w-full border rounded px-3 py-2">{{ old('content', $tweet->content) }}</textarea>
            @error('content')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Update
            </button>
            <a href="{{ route('tweets.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
