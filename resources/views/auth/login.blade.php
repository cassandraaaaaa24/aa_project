@extends('layouts.app')

@section('content')
<div class="card form-card">
    <h2 class="form-title">Login</h2>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input">
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

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Login
            </button>
        </div>
    </form>
</div>
@endsection
