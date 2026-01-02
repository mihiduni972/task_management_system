<h2>User Details</h2>

<table class="table">
    <tr>
        <th>Full Name</th>
        <td>{{ $user->full_name }}</td>
    </tr>
    <tr>
        <th>Username</th>
        <td>{{ $user->username }}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{{ $user->email }}</td>
    </tr>
    <tr>
        <th>Birthdate</th>
        <td>{{ $user->birthdate ?? '-' }}</td>
    </tr>
    <tr>
        <th>User Type</th>
        <td>{{ ucfirst($user->user_type) }}</td>
    </tr>
</table>

<a href="{{ route('admin.users') }}" class="btn btn-secondary">Back</a>
