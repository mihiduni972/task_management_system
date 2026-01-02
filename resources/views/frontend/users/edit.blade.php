@extends('frontend.layouts.app')

@section('content')

<div class="users-wrapper full-width">

    {{-- EDIT USER MODAL --}}
    <div class="modal" style="display:flex;">
        <div class="modal-content">

            <h3 style="text-align:center;margin-bottom:20px;">
                Edit User
            </h3>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="success-box">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <label>Full Name</label>
                <input type="text"
                       name="full_name"
                       value="{{ old('full_name', $user->full_name) }}"
                       required>

                <label>Username</label>
                <input type="text"
                       name="username"
                       value="{{ old('username', $user->username) }}"
                       required>

                <label>Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       required>

                <label>Birthdate</label>
                <input type="date"
                       name="birthdate"
                       value="{{ old('birthdate', $user->birthdate) }}">

                <label>User Type</label>
                <select name="user_type" required>
                    <option value="admin"
                        {{ old('user_type', $user->user_type) === 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>
                    <option value="employer"
                        {{ old('user_type', $user->user_type) === 'employer' ? 'selected' : '' }}>
                        Employer
                    </option>
                </select>

                <div class="form-actions">
                    <button type="submit" class="save-btn">
                        Update User
                    </button>

                    <a href="{{ route('admin.users') }}"
                       class="cancel-btn">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

{{-- ================= CSS ================= --}}
<style>
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:1000;
}
.modal-content{
    background:#fff;
    padding:30px;
    border-radius:20px;
    width:500px;
    max-width:90%;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}
label{
    display:block;
    margin-top:10px;
    font-weight:500;
}
input,select{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:12px;
    border:1px solid #ccc;
}
.form-actions{
    display:flex;
    gap:10px;
    margin-top:20px;
}
.save-btn{
    flex:1;
    background:#7c1616;
    color:#fff;
    padding:12px;
    border:none;
    border-radius:20px;
    cursor:pointer;
}
.cancel-btn{
    flex:1;
    background:#e5e7eb;
    color:#000;
    padding:12px;
    border-radius:20px;
    text-align:center;
    text-decoration:none;
}
.success-box{
    background:#e6f9ed;
    color:#065f46;
    padding:12px;
    border-radius:12px;
    margin-bottom:15px;
}
.error-box{
    background:#fee2e2;
    color:#991b1b;
    padding:12px;
    border-radius:12px;
    margin-bottom:15px;
}
</style>

@endsection
