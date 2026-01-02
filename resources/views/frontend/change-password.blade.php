@extends('frontend.layouts.app')

@section('content')

<div class="card" style="max-width:400px;margin:auto;">
    <h2 style="text-align:center;margin-bottom:15px;">
        Change Password
    </h2>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="success-box">{{ session('success') }}</div>
    @endif

    {{-- ERROR --}}
    @if(session('error'))
        <div class="error-box">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf

        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="password" required>

        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" required>

        <button class="save-btn">Update Password</button>
    </form>
</div>

@endsection
