<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Event Organizer') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{ config('app.name', 'Event Organizer') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}">Events</a>
                        </li>

                        @auth
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'creator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('events.create') ? 'active' : '' }}" href="{{ route('events.create') }}">Create Event</a>
                                </li>
                            @endif

                            @if(Auth::user()->role === 'creator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('events.my-events') ? 'active' : '' }}" href="{{ route('events.my-events') }}">My Events</a>
                                </li>
                            @endif

                            @if(Auth::user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin</a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <div class="d-flex">
                        @auth
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ '/storage/' . Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="avatar">
                                    @else
                                        <div class="avatar">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><span class="dropdown-item-text">Logged in as <strong>{{ Auth::user()->name }}</strong></span></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('profile') }}">Your Profile</a></li>

                                    @if(Auth::user()->role === 'creator')
                                        <li><a class="dropdown-item" href="{{ route('participants.my-event-participants') }}">Manage Participants</a></li>
                                    @endif

                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Sign out</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Log in</a>
                            <a href="{{ route('register') }}" class="btn btn-primary">Sign up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('home') }}" class="navbar-brand">
                            {{ config('app.name', 'Event Organizer') }}
                        </a>
                        <p class="mt-2 text-muted">Organize events with ease.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="text-muted">
                            &copy; {{ date('Y') }} Event Organizer. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>