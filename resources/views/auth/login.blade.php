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

<style>
    /* Fix input field overflow */
    .form-input {
        width: 100%;
        box-sizing: border-box; /* Include padding in width */
        max-width: 100%; /* Prevent overflow */
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .form-card {
        max-width: 500px;
        margin: 40px auto;
        padding: 32px;
        box-sizing: border-box;
    }

    .form-title {
        margin-bottom: 24px;
        text-align: center;
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .form-actions {
        margin-top: 24px;
    }

    .btn {
        width: 100%;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .error {
        color: #dc3545;
        font-size: 13px;
        display: block;
        margin-top: 6px;
    }

    @media (max-width: 768px) {
        .form-card {
            margin: 20px;
            padding: 24px;
        }

        .form-title {
            font-size: 24px;
        }
    }
</style>
@endsection