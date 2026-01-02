<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TOYAR</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
</head>
<body>

{{-- ================= TOP NAV ================= --}}
<header class="top-nav">
    <div class="nav-left">
        <img src="/images/toyar.png" class="nav-logo">
        <span class="nav-title">TOYAR</span>
    </div>

    @auth
    <div class="nav-right">
        <div class="profile-dropdown">
            <button class="profile-btn" onclick="toggleProfile(event)">
                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>
                <span class="profile-name">
                    {{ auth()->user()->username }}
                </span>
            </button>

            <div class="dropdown-menu" id="profileMenu">
                <a href="{{ route('profile.index') }}">Profile</a>
            </div>
        </div>
    </div>
    @endauth
</header>

{{-- ================= APP CONTAINER ================= --}}
<div class="app-container">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="sidebar">
        <div class="sidebar-title">DEV SYSTEM</div>

        @auth
            <a href="{{ route('tasks.index') }}"
               class="menu-btn {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                Daily Tasks
            </a>

            @if(auth()->user()->user_type === 'admin')
                <a href="{{ route('admin.users') }}"
                   class="sidebar-btn {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    Users
                </a>
            @endif

            {{-- 🔴 LOGOUT --}}
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        @endauth
    </aside>

    {{-- ================= CONTENT ================= --}}
    <main class="content">
        @yield('content')
    </main>

</div>

{{-- ================= TOAST SUCCESS ================= --}}
@if(session('success'))
    <div id="toast-success" class="toast-bottom-right">
    {{ session('success') }}
</div>

@endif

{{-- ================= SCRIPTS ================= --}}
<script>
    function toggleProfile(event) {
        event.stopPropagation();
        const menu = document.getElementById('profileMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', () => {
        const menu = document.getElementById('profileMenu');
        if (menu) menu.style.display = 'none';
    });

    // auto remove toast after 5 sec
    setTimeout(() => {
        const toast = document.getElementById('toast-success');
        if (toast) toast.remove();
    }, 5000);
</script>

</body>
</html>
