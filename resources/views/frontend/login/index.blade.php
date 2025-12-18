<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="bg">
    <div class="login-wrapper">

        <!-- LEFT SIDE -->
        <div class="login-form">
            <h1>Welcome</h1>
            <p>Sign in entering the information below</p>

            <input type="text" placeholder="User Name">
            <input type="password" placeholder="Password">

            <button>Sign in</button>
        </div>

        <!-- RIGHT SIDE -->
        <div class="login-image">
            <img src="{{ asset('images/login.jpeg') }}" alt="Login Image">
        </div>

    </div>
</div>

</body>
</html>
