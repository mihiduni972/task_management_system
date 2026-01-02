<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TOYAR</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">

    <!-- ADD THIS STYLE -->
    <style>
        .add-task-form {
            display: none;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<!-- ================= TOP NAVIGATION ================= -->
<header class="top-nav">
    <img src="/images/toyar.png" class="nav-logo">
    <span class="nav-title">TOYAR</span>
</header>

<!-- ================= MAIN APP ================= -->
<div class="app-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="{{ route('tasks.index') }}" class="menu-btn">
            Daily Tasks
        </a>

        <div class="sidebar-bottom">
            <a href="#" class="profile-btn">
                <img src="/images/profile.jpg">
                <span>John Doe</span>
            </a>

            <button class="logout-btn">Logout</button>
        </div>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        @yield('content')
    </main>

</div>

<!-- ================= ADD THIS SCRIPT ================= -->
<script>
    function showAddTaskForm() {
        const form = document.getElementById('addTaskForm');

        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth' });
        } else {
            form.style.display = 'none';
        }
    }
</script>

</body>
</html>
