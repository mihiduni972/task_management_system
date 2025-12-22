<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TOYAR</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
        <a href="#" class="menu-btn">Daily Tasks</a>

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
        <div class="task-card">
            <h2>ADD TASK</h2>

            <label>Task</label>
            <input type="text">

            <label>Sub Task</label>
            <input type="text">

            <label>Status</label>
            <select>
                <option>Pending</option>
                <option>Completed</option>
            </select>

            <div class="date-row">
                <div class="date-field">
                    <label>Start Date</label>
                    <input type="date">
                </div>
                <div class="date-field">
                    <label>End Date</label>
                    <input type="date">
                </div>
            </div>

            <button class="create-btn">CREATE</button>
        </div>
    </main>

</div>

</body>
</html>
