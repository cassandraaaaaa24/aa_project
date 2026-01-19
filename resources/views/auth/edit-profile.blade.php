@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border rounded px-3 py-2">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Biography -->
        <div class="mb-4">
            <label for="bio" class="block text-sm font-medium">Biography</label>
            <textarea id="bio" name="bio" rows="4" class="w-full border rounded px-3 py-2"
                      placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Profile Picture -->
        <div class="mb-4">
            <label for="profile_picture" class="block text-sm font-medium">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                   class="w-full border rounded px-3 py-2">
            @error('profile_picture')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            
            @if($user->profile_picture)
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Current profile picture:</p>
                    <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                </div>
            @endif
        </div>

        <!-- Submit -->
        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Save Changes
            </button>
            <a href="{{ route('users.show', $user->id) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
