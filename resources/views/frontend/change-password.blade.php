@extends('frontend.layouts.app')

@section('content')

<div class="add-task-card">
    <h2>Change Password</h2>

    <form method="POST" action="/change-password">
        @csrf

        <label>New Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>

        <button class="create-btn">UPDATE</button>
    </form>
</div>

@endsection
