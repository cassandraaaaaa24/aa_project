<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yappr</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <a href="{{ url('/') }}" class="brand">Yappr</a>
            <div class="nav-links">
                @auth
                    <a href="{{ route('users.show', Auth::id()) }}" class="profile-link" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 6px; transition: background-color 0.2s;">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                 alt="Your profile" 
                                 style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;"
                                 onerror="this.style.display='none';">
                        @endif
                        <span class="welcome">{{ Auth::user()->name }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                @endauth
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} Yappr. All rights reserved.</p>
        </footer>
    </div>

    <style>
        /* Profile link hover effect */
        .profile-link:hover {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }

        .profile-link:hover .welcome {
            color: #007bff !important;
        }
    </style>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>