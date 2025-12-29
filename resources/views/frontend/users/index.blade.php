@extends('frontend.layouts.app')

@section('content')

<div class="users-wrapper full-width">

    {{-- Header --}}
    <div class="users-header">
        <h2>All Users</h2>

        <form method="GET">
            <input
                type="text"
                name="search"
                placeholder="Search username"
                value="{{ request('search') }}"
            >
        </form>

        <button class="add-btn" onclick="openForm()">+ Add User</button>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="success-box">{{ session('success') }}</div>
    @endif

    {{-- USERS TABLE --}}
    <div class="card">
        <table>
            {{-- UPDATED THEAD --}}
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Birthdate</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            {{-- UPDATED TBODY --}}
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->birthdate ?? '-' }}</td>

                        <td>
                            <span class="role {{ $user->user_type }}">
                                {{ ucfirst($user->user_type) }}
                            </span>
                        </td>

                        <td>
                            <span class="status pending">Active</span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="btn-edit">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.users.delete', $user) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn-delete">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="pagination">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- ADD USER MODAL --}}
<div class="modal" id="userForm">
    <div class="modal-content">
        <h3>Add New User</h3>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Birthdate</label>
            <input type="date" name="birthdate">

            <label>Password</label>
            <input type="password" name="password" required>

            <label>User Type</label>
            <select name="user_type" required>
                <option value="">Select</option>
                <option value="admin">Admin</option>
                <option value="employer">Employer</option>
            </select>

            <button type="submit" class="save-btn">
                Save User
            </button>

            <button type="button"
                    class="cancel-btn"
                    onclick="closeForm()">
                Cancel
            </button>
        </form>
    </div>
</div>

{{-- JS --}}
<script>
    function openForm() {
        document.getElementById('userForm').style.display = 'flex';
    }

    function closeForm() {
        document.getElementById('userForm').style.display = 'none';
    }
</script>

@endsection
