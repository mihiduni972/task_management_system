@extends('frontend.layouts.app')


@section('content')
<div class="card" style="max-width:600px;margin:auto;">
    <h2>My Profile</h2>

    

    {{-- USER DETAILS --}}
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf

        <label>Full Name</label>
        <input type="text" name="name" value="{{ auth()->user()->name }}">

        <label>Username</label>
        <input type="text" name="username" value="{{ auth()->user()->username }}">

        <button class="save-btn">Update Profile</button>
    </form>

    <hr style="margin:25px 0;">

    {{-- CHANGE PASSWORD --}}
    <form action="{{ route('profile.password') }}" method="POST">
        @csrf

        <label>New Password</label>
        <input type="password" name="password">

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation">

        <button class="save-btn">Change Password</button>
    </form>
</div>
@endsection
