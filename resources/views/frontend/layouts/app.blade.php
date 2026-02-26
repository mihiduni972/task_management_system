<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TOYAR</title>
    
    {{-- GLOBAL CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leave.css') }}">
    <link rel="stylesheet" href="{{ asset('css/project.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css')}}">
    <link rel="stylesheet" href="{{ asset('css/assets.css') }}">
    <link rel="stylesheet" href="{{ asset('css/worklog.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    {{-- Font Awesome for Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- ===== GLOBAL TOAST & SIDEBAR DROPDOWN CSS ===== --}}
    <style>
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #ffffff;
            color: #000000;
            padding: 14px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
            transition: all 0.4s ease;
            z-index: 9999;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .notification-count {
            background: red;
            color: white;
            font-size: 0.6rem;
            border-radius: 50%;
            padding: 2px 6px;
            position: absolute;
            top: 0;
            right: 0;
        }

        .notification-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 9999;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .mark-read-btn {
            font-size: 0.75rem;
            background: none;
            border: none;
            color: blue;
            cursor: pointer;
            padding: 0;
            margin-top: 4px;
        }

        /* Sidebar Dropdown Styling */
        .sidebar-dropdown-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            text-decoration: none !important;
        }
        .dropdown-arrow {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
            padding: 5px;
        }
        
        .sub-menu {
            list-style: none;
            padding-left: 15px;
            background: rgba(0,0,0,0.03);
            margin: 0;
            display: none; 
        }
        .sub-menu .menu-btn {
            font-size: 0.85rem;
            padding: 10px 15px;
            border-left: 2px solid transparent;
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .sub-menu .menu-btn.active {
            border-left: 2px solid #7b1b1b;
            background: transparent;
            color: #7b1b1b;
        }
        
    </style>

    @stack('styles')
</head>
<body>

<div id="toast" class="toast"></div>

<header class="top-nav">
    <div class="nav-left">
        <img src="/images/toyar.png" class="nav-logo">
        <span class="nav-title">TOYAR</span>
    </div>

    {{-- 🔍 GLOBAL SEARCH BAR --}}
    @auth
    <form action="{{ route('global.search') }}" method="GET" class="global-search-form">
    <input type="text" name="q" placeholder="Search..." value="{{ request('q') }}">
    <button type="submit"><i class="fas fa-search"></i></button>
</form>

    @endauth


    @auth
    <div class="nav-right d-flex align-items-center">
        <div class="profile-dropdown">
    <button class="profile-btn" onclick="toggleProfile(event)">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <span class="profile-name">
            {{ auth()->user()->user_type === 'admin' ? 'Admin' : auth()->user()->profile_display_name }}
        </span>
    </button>
    
    <div class="dropdown-menu" id="profileMenu">
        <a href="{{ route('profile.index') }}" style="display: inline-block; width: auto; padding-right: 5px;">Profile</a>
        
        <span style="color: #ccc;">/</span>
        
        <a href="#" style="display: inline-block; width: auto; padding-left: 5px; " 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           Logout
        </a>
<span style="color: #ccc;"></span>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
        <div class="notifications-dropdown ms-3" style="position: relative;">
            <button class="notification-btn" onclick="toggleNotifications(event)">
                🔔
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="notification-count">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </button>
            <div class="notification-menu" id="notificationMenu">
                @forelse(auth()->user()->unreadNotifications as $notification)
                    <div class="notification-item">
                        <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                        <p>{{ $notification->data['message'] ?? 'You have a new update' }}</p>
                        <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                            @csrf
                            <button class="mark-read-btn">Mark as read</button>
                        </form>
                    </div>
                @empty
                    <div class="notification-item text-center">No new notifications</div>
                @endforelse
            </div>
        </div>
    </div>
    @endauth
</header>

<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-title">DEV SYSTEM</div>

        @auth
            {{-- EMPLOYER SECTION --}}
            @if(auth()->user()->user_type === 'employer')
                <div class="nav-item">
                    <a class="menu-btn sidebar-dropdown-btn {{ Request::is('tasks*') ? 'active' : '' }}" 
                       href="/tasks">
                        <span>Daily Tasks</span>
                        <i class="fas fa-chevron-down dropdown-arrow" 
                           id="dailyArrow" 
                           onclick="toggleDailyTasks(event)"></i>
                    </a>
                    
                    <div id="dailyTasksSubMenu" style="display: none;">
                        <ul class="sub-menu" style="display: block; list-style: none; padding-left: 20px; margin-top: 5px;">
                            <li>
                                <a href="{{ route('time-logs.my-logs') }}" 
                                   class="menu-btn {{ request()->routeIs('time-logs.my-logs') ? 'active' : '' }}">
                                    <i class="fas fa-history me-2"></i> My Logs
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <a href="{{ route('attendance.employer.index') }}"
                    class="menu-btn {{ request()->routeIs('attendance.employer.*') ? 'active' : '' }}">
                    My Attendance
                </a>

                <a href="{{ route('leaves.index') }}"
                    class="menu-btn {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                    My Leaves
                </a>

                <a href="{{ route('employer.projects') }}"
                    class="menu-btn {{ request()->routeIs('employer.projects') || request()->routeIs('employer.projects.*') ? 'active' : '' }}">
                    My Projects
                </a>
            @endif

            {{-- ATTENDANCE MARKER SECTION --}}
            @if(auth()->user()->user_type === 'attendance_marker')
                <a href="{{ route('attendance.mark') }}"
                    class="menu-btn {{ request()->routeIs('attendance.mark') ? 'active' : '' }}">
                    Attendance
                </a>
            @endif

            {{-- ADMIN SECTION --}}
            @if(auth()->user()->user_type === 'admin')
                <a href="{{ route('admin.reports.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    Dashboard
                </a>

                {{-- NEW: Attendance Analytics Dashboard --}}
                 <a href="{{ route('admin.attendance.dashboard') }}"
                        class="menu-btn {{ request()->routeIs('admin.attendance.dashboard') ? 'active' : '' }}">
                         Attendance Dashboard
                        </a>


                <a href="{{ route('admin.attendance.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}">
                    Attendance
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    Users
                </a>
                <a href="{{ route('admin.tasks.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                    Employer Tasks
                </a>
                
                {{-- NEW: Work Logs Management Link for Admin --}}
                <a href="{{ route('admin.time-logs.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.time-logs.*') ? 'active' : '' }}">
                    Work Logs Management
                </a>

                <a href="{{ route('admin.leaves.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
                    Leave Approvals
                </a>
                <a href="{{ route('admin.projects.index') }}" 
                    class="menu-btn {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                    Projects
                </a>
                <a href="{{ route('admin.clients.index') }}"
                    class="menu-btn {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                    Clients
                </a>
                <a href="{{ route('assets.index') }}"
   class="menu-btn {{ request()->routeIs('assets.*') ? 'active' : '' }}">
   Asset Management
</a>
            @endif

            
        @endauth
    </aside>

    <main class="content">
        @yield('content')
    </main>
</div>

{{-- Scripts Section --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Profile Dropdown
    function toggleProfile(event) {
        event.stopPropagation();
        const menu = document.getElementById('profileMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    // Toggle Notifications Dropdown
    function toggleNotifications(event) {
        event.stopPropagation();
        const menu = document.getElementById('notificationMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    // Toggle Sidebar Sub-menu for Daily Tasks
    function toggleDailyTasks(event) {
        event.stopPropagation();
        event.preventDefault();
        
        const menu = document.getElementById('dailyTasksSubMenu');
        const arrow = document.getElementById('dailyArrow');
        
        if (menu.style.display === "none" || menu.style.display === "") {
            menu.style.display = "block";
            arrow.style.transform = "rotate(180deg)";
        } else {
            menu.style.display = "none";
            arrow.style.transform = "rotate(0deg)";
        }
    }

    // Close menus when clicking anywhere on document
    document.addEventListener('click', () => {
        const profileMenu = document.getElementById('profileMenu');
        const notifMenu = document.getElementById('notificationMenu');
        if (profileMenu) profileMenu.style.display = 'none';
        if (notifMenu) notifMenu.style.display = 'none';
    });

    // Toast Notification System
    function showToast(message) {
        const toast = document.getElementById('toast');
        if(toast) {
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    }
</script>

@stack('scripts')
</body>
@yield('scripts')
</html>