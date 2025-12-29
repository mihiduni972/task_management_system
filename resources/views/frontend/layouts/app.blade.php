<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TOYAR</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
</head>
<body>

<header class="top-nav">
    <img src="/images/toyar.png" class="nav-logo">
    <span class="nav-title">TOYAR</span>
</header>

<div class="app-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        @auth
<a href="{{ route('tasks.index') }}"
   class="menu-btn">
   Daily Tasks
</a>


        <!-- USERS (ADMIN ONLY) -->
        @if(auth()->user()->user_type === 'admin')
   <a href="{{ route('admin.users') }}"
   class="sidebar-btn {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
    Users
</a>

@endif

        @endauth

        <!-- BOTTOM -->
        <div class="sidebar-bottom">
            <a href="{{ route('password.change') }}" class="profile-btn">
                <img src="/images/profile.jpg">
                @auth
                    <span>{{ auth()->user()->username }}</span>
                @endauth
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn">Logout</button>
            </form>
        </div>

    </aside>

    <!-- PAGE CONTENT -->
    <main class="content">
        @yield('content')
    </main>

</div>

</body>
</html>
