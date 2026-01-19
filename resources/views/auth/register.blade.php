@extends('layouts.app')

@section('content')
<div class="card form-card">
    <h2 class="form-title">Create an Account</h2>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input">
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input">
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required class="form-input">
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input">
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-secondary">
                Sign Up
            </button>
        </div>
    </form>
</div>
@endsection
