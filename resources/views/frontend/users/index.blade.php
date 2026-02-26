@extends('frontend.layouts.app')

@section('content')


<div class="users-wrapper full-width">

    {{-- ================= HEADER ================= --}}
    <div class="users-header">
        <div class="header-left">
            <h2>All Users</h2>
        </div>

        <div class="header-right">
            <form method="GET" class="search-form" style="margin-top:10px;">
                <input type="text" name="search" placeholder="Search username" value="{{ request('search') }}">
            </form>

            <button class="add-btn-user" onclick="openAddModal()">+ Add User</button>
        </div>
    </div>
    <style>
       
    .pagination-wrapper nav div:first-child,
    .pagination-wrapper nav p {
        display: none !important;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 25px;
    }

    .pagination {
        display: flex !important;
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 0 !important;
    }

    .pagination li a, 
    .pagination li span {
        min-width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0 !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-left: -1px;
    }

    .pagination li.active span {
        background: #000000 !important;
        color: #ffffff !important;
        border-color: #000000 !important;
    }

    .pagination li a:hover {
        background-color: #f3f4f6 !important;
        color: #000000 !important;
    }
    .custom-toast {
    position: fixed;
    bottom: 30px;   
    right: 30px;
    background: #fff;
    color: #111;
    padding: 14px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    z-index: 9999;
    opacity: 0;
    transform: translateY(20px); 
    transition: all 0.4s ease;
}

.custom-toast.show {
    opacity: 1;
    transform: translateY(0);
}
        </style>
        @if(session('success'))
<div id="toast-success" class="custom-toast">
    {{ session('success') }}
</div>
@endif

{{-- ================= USERS TABLE ================= --}}
<div class="card" style="padding:20px;">
    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">

        <thead>
            <tr style="background:#f3f3f3;">
                <th style="width:6%; padding:12px 8px; text-align:center;">#</th>
                <th style="width:22%; padding:12px 8px; text-align:center;">Full Name</th>
                <th style="width:15%; padding:12px 8px; text-align:center;">Username</th>
                <th style="width:15%; padding:12px 8px; text-align:center;">Phone</th>
                <th style="width:14%; padding:12px 8px; text-align:center;">Role</th>
                <th style="width:10%; padding:12px 8px; text-align:center;">Status</th>
                <th style="width:18%; padding:12px 8px; text-align:center;">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
            <tr style="border-bottom:1px solid #eee;">

                <td style="padding:12px 8px; text-align:center;">
                    {{ $user->id }}
                </td>

                <td style="padding:12px 8px; text-align:center;">
                    {{ $user->full_name }}
                </td>

                <td style="padding:12px 8px; text-align:center;">
                    {{ $user->username }}
                </td>

                <td style="padding:12px 8px; text-align:center;">
                    {{ $user->phone ?? '-' }}
                </td>

                <td style="padding:12px 8px; text-align:center;">
                    {{ ucfirst($user->user_type) }}
                </td>

                <td style="padding:12px 8px; text-align:center;">
                    Active
                </td>

                <td style="padding:12px 8px; text-align:center;">
                    <div style="display:flex; gap:6px; justify-content:center;">

                        <button style="padding:6px 12px; border:none; background:#e5e7eb; border-radius:6px; cursor:pointer;"
                            onclick="openViewModal(
                                '{{ addslashes($user->profile_display_name ?? $user->full_name) }}',
                                '{{ addslashes($user->username) }}',
                                '{{ addslashes($user->email) }}',
                                '{{ $user->phone ?? '-' }}',
                                '{{ $user->birthdate ?? '-' }}',
                                '{{ ucfirst($user->user_type) }}',
                                '{{ ucfirst($user->gender ?? '-') }}'
                            )">
                            View
                        </button>

                        <button style="padding:6px 12px; border:none; background:#e5e7eb; border-radius:6px; cursor:pointer;"
                            onclick='openEditModal(@json($user))'>
                            Edit
                        </button>

                        <form method="POST"
                              action="{{ route('admin.users.destroy', $user->id) }}"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button style="padding:6px 12px; border:none; background:#e5e7eb; border-radius:6px; cursor:pointer;"
                                    onclick="return confirm('Delete this user?')">
                                Delete
                            </button>
                        </form>

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding:20px; text-align:center;">
                    No users found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{-- PAGINATION LINKS --}}
    <div class="pagination-wrapper mt-3">
        {{ $users->links() }}
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

            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone</label>
            <input type="text" name="phone">

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
<div class="modal-actions right">
    <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
    <button type="submit" class="save-btn">Save</button>
</div>

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

            <label>Gender</label>
            <select name="gender" id="edit_gender" required>
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label>Username</label>
            <input type="text" name="username" id="edit_username">

            <label>Email</label>
            <input type="email" name="email" id="edit_email">

            <label>Phone</label>
            <input type="text" name="phone" id="edit_phone">

            <label>Birthdate</label>
            <input type="date" name="birthdate" id="edit_birthdate">

            <label>User Type</label>
            <select name="user_type" id="edit_user_type">
                <option value="admin">Admin</option>
                <option value="employer">Employer</option>
            </select>

            <div class="modal-actions">
    <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
    <button type="submit" class="save-btn">Update</button>
</div>

        </form>
    </div>
</div>

{{-- ================= VIEW USER MODAL ================= --}}
<div class="modal" id="viewUserModal" onclick="closeOnOutside(event, 'viewUserModal')">
    <div class="modal-content view-modal">
        <h3>User Details</h3>

        <div class="view-form">

            <div class="view-row">
                <span class="label">Full Name</span>
                <span class="value" id="v_full"></span>
            </div>

            <div class="view-row">
                <span class="label">Username</span>
                <span class="value" id="v_user"></span>
            </div>

            <div class="view-row">
                <span class="label">Email</span>
                <span class="value" id="v_email"></span>
            </div>

            <div class="view-row">
                <span class="label">Phone</span>
                <span class="value" id="v_phone"></span>
            </div>

            <div class="view-row">
                <span class="label">Birthdate</span>
                <span class="value" id="v_birth"></span>
            </div>

            <div class="view-row">
                <span class="label">User Type</span>
                <span class="value badge" id="v_type"></span>
            </div>

            <div class="view-row">
                <span class="label">Gender</span>
                <span class="value" id="v_gender"></span>
            </div>

        </div>

        <div class="view-actions">
            <button type="button" class="cancel-btn" onclick="closeViewModal()">Close</button>
        </div>
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
    document.getElementById('edit_gender').value = user.gender;
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

function openViewModal(n,u,e,p,b,t,g) {
    document.getElementById('v_full').innerText = n;
    document.getElementById('v_user').innerText = u;
    document.getElementById('v_email').innerText = e;
    document.getElementById('v_phone').innerText = p;
    document.getElementById('v_birth').innerText = b;
    document.getElementById('v_type').innerText = t;
    document.getElementById('v_gender').innerText = g;
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
document.addEventListener("DOMContentLoaded", function () {
    const toast = document.getElementById("toast-success");
    if (toast) {
        setTimeout(() => {
            toast.classList.add("show");
        }, 100);

        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000);

        setTimeout(() => {
            toast.remove();
        }, 3500);
    }
});

</script>

@endsection
