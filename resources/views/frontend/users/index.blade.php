@extends('frontend.layouts.app')

@section('content')

<div class="users-wrapper full-width">

    {{-- ================= HEADER ================= --}}
    <div class="users-header">
        <div class="header-left">
            <h2>All Users</h2>
        </div>

        <div class="header-right">
            <div>
                <form method="GET" class="search-form" >
                    <input type="text" name="search" placeholder="Search username" value="{{ request('search') }}">
                </form>
</div>

            <button class="add-btn" onclick="openAddModal()">+ Add User</button>
        </div>
    </div>

    {{-- ================= USERS TABLE ================= --}}
    <div class="card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ ucfirst($user->user_type) }}</td>
                    <td>Active</td>
                    <td>
                        <div class="action-row">

                            <button class="btn-view"
                                onclick="openViewModal(
                                    '{{ addslashes($user->full_name) }}',
                                    '{{ addslashes($user->username) }}',
                                    '{{ addslashes($user->email) }}',
                                    '{{ $user->phone ?? '-' }}',
                                    '{{ $user->birthdate ?? '-' }}',
                                    '{{ ucfirst($user->user_type) }}'
                                )">
                                View
                            </button>

                            <button class="btn-edit"
                                onclick='openEditModal(@json($user))'>
                                Edit
                            </button>

                            <form method="POST"
                                  action="{{ route('admin.users.destroy', $user->id) }}"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn-delete"
                                        onclick="return confirm('Delete this user?')">
                                    Delete
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $users->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

{{-- ================= ADD USER MODAL ================= --}}
<div class="modal" id="addUserModal" onclick="closeOnOutside(event, 'addUserModal')">
    <div class="modal-content">
        <h3>Add User</h3>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="07XXXXXXXX" required>

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

            <button class="save-btn">Save</button>
            <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
        </form>
    </div>
</div>

{{-- ================= EDIT USER MODAL ================= --}}
<div class="modal" id="editUserModal" onclick="closeOnOutside(event, 'editUserModal')">
    <div class="modal-content">
        <h3>Edit User</h3>

        <form method="POST" id="editUserForm">
            @csrf
            @method('PUT')

            <label>Full Name</label>
            <input type="text" name="full_name" id="edit_full_name">

            <label>Username</label>
            <input type="text" name="username" id="edit_username">

            <label>Email</label>
            <input type="email" name="email" id="edit_email">

            <label>Phone Number</label>
            <input type="text" name="phone" id="edit_phone">

            <label>Birthdate</label>
            <input type="date" name="birthdate" id="edit_birthdate">

            <label>User Type</label>
            <select name="user_type" id="edit_user_type">
                <option value="admin">Admin</option>
                <option value="employer">Employer</option>
            </select>

            <button class="save-btn">Update</button>
            <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>
</div>

{{-- ================= VIEW USER MODAL ================= --}}
<div class="modal" id="viewUserModal" onclick="closeOnOutside(event, 'viewUserModal')">
    <div class="modal-content">
        <h3>User Details</h3>

        <table class="view-table">
            <tr><th>Full Name</th><td id="v_full"></td></tr>
            <tr><th>Username</th><td id="v_user"></td></tr>
            <tr><th>Email</th><td id="v_email"></td></tr>
            <tr><th>Phone</th><td id="v_phone"></td></tr>
            <tr><th>Birthdate</th><td id="v_birth"></td></tr>
            <tr><th>User Type</th><td id="v_type"></td></tr>
        </table>

        <button class="cancel-btn" onclick="closeViewModal()">Close</button>
    </div>
</div>

{{-- ================= JS ================= --}}
<script>
function openAddModal() {
    document.getElementById('addUserModal').style.display = 'flex';
}
function closeAddModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function openEditModal(user) {
    document.getElementById('editUserModal').style.display = 'flex';
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone ?? '';
    document.getElementById('edit_birthdate').value = user.birthdate;
    document.getElementById('edit_user_type').value = user.user_type;
    document.getElementById('editUserForm').action = `/admin/users/${user.id}`;
}
function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

function openViewModal(n,u,e,p,b,t) {
    document.getElementById('v_full').innerText = n;
    document.getElementById('v_user').innerText = u;
    document.getElementById('v_email').innerText = e;
    document.getElementById('v_phone').innerText = p;
    document.getElementById('v_birth').innerText = b;
    document.getElementById('v_type').innerText = t;
    document.getElementById('viewUserModal').style.display = 'flex';
}
function closeViewModal() {
    document.getElementById('viewUserModal').style.display = 'none';
}

function closeOnOutside(event, id) {
    if (event.target.id === id) {
        document.getElementById(id).style.display = 'none';
    }
}

document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    }
});
</script>

@endsection
