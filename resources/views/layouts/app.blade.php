<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter Clone</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <a href="{{ url('/') }}" class="brand">Twitter Clone</a>
            <div class="nav-links">
                @auth
                    <span class="welcome">Hello, {{ Auth::user()->name }}</span>
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
            <p>&copy; {{ date('Y') }} Twitter Clone. All rights reserved.</p>
        </footer>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
