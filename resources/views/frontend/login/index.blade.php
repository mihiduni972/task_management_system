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

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- LOGIN FORM --}}
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <input type="text"
                       name="username"
                       placeholder="User Name"
                       required>

                <input type="password"
                       name="password"
                       placeholder="Password"
                       required>

                <button type="submit">Sign in</button>
            </form>
        </div>

        <!-- RIGHT SIDE -->
        <div class="login-image">
            <img src="{{ asset('images/login.jpeg') }}" alt="Login Image">
        </div>

    </div>
</div>

</body>
</html>

