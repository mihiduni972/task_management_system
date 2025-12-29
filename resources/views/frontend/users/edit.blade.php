@extends('frontend.layouts.app')

@section('content')

<div class="users-wrapper full-width">

    <div class="card" style="max-width:500px;margin:auto;">
        <h2 style="text-align:center;margin-bottom:20px;">
            Edit User
        </h2>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="success-box">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR --}}
        @if ($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:12px;margin-bottom:15px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <label>Username</label>
            <input
                type="text"
                name="username"
                value="{{ old('username', $user->username) }}"
                required
            >

            <label>User Type</label>
            <select name="user_type" required>
                <option value="admin"
                    {{ $user->user_type === 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="employer"
                    {{ $user->user_type === 'employer' ? 'selected' : '' }}>
                    Employer
                </option>
            </select>

            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="save-btn">
                    Update User
                </button>

                <a href="{{ route('admin.users') }}"
                   class="cancel-btn"
                   style="text-align:center;line-height:38px;text-decoration:none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>

@endsection
