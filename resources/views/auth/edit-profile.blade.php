@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 style="margin-bottom: 20px;">Edit Profile</h2>

        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- Current Profile Picture -->
            @if($user->profile_picture)
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                         alt="Current Profile Picture" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                    <p style="margin-top: 10px; color: #666; font-size: 14px;">Current Profile Picture</p>
                </div>
            @endif

            <!-- Profile Picture Upload -->
            <div style="margin-bottom: 20px;">
                <label for="profile_picture" style="display: block; margin-bottom: 5px; font-weight: bold;">
                    Profile Picture
                </label>
                <input type="file" 
                       id="profile_picture" 
                       name="profile_picture" 
                       accept="image/*" 
                       class="input-field">
                @error('profile_picture')
                    <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                @enderror
                <p style="margin-top: 5px; color: #666; font-size: 12px;">
                    Allowed formats: JPEG, PNG, GIF, WEBP (Max: 2MB)
                </p>
            </div>

            <!-- Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold;">
                    Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       class="input-field" 
                       required>
                @error('name')
                    <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">
                    Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}" 
                       class="input-field" 
                       required>
                @error('email')
                    <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Bio -->
            <div style="margin-bottom: 20px;">
                <label for="bio" style="display: block; margin-bottom: 5px; font-weight: bold;">
                    Bio
                </label>
                <textarea id="bio" 
                          name="bio" 
                          rows="4" 
                          class="textarea" 
                          placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                @enderror
                <p style="margin-top: 5px; color: #666; font-size: 12px;">
                    Maximum 500 characters
                </p>
            </div>

            <!-- Change Password Section -->
            <div style="border-top: 1px solid #ddd; padding-top: 20px; margin-top: 30px;">
                <h3 style="margin-bottom: 15px; font-size: 18px;">Change Password (Optional)</h3>
                
                <!-- New Password -->
                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">
                        New Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="input-field" 
                           placeholder="Leave blank to keep current password">
                    @error('password')
                        <span style="color: #dc3545; font-size: 14px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 20px;">
                    <label for="password_confirmation" style="display: block; margin-bottom: 5px; font-weight: bold;">
                        Confirm New Password
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="input-field" 
                           placeholder="Confirm your new password">
                </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    Update Profile
                </button>
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection