<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 🔹 Display a paginated list of users (Admin page)
     * GET /admin/users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 🔍 Apply search filter by username if provided
        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
        }

        // Paginate results, 6 users per page, ordered by newest first
        $users = $query->orderBy('created_at', 'desc')->paginate(6);

        return view('frontend.users.index', compact('users'));
    }

    /**
     * 🔹 Store a new user (Admin / Employer / Attendance)
     * POST /admin/users
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender'    => 'required|in:male,female',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|string|max:20|unique:users,phone',
            'birthdate' => 'nullable|date',
            'password'  => 'required|min:6',
            'user_type' => 'required|in:admin,employer,attendance',
        ]);

        // Create user
        User::create([
            'full_name' => $request->full_name,
            'gender'    => $request->gender,
            'username'  => $request->username,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'birthdate' => $request->birthdate,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User added successfully!');
    }

    /**
     * 🔹 Update an existing user
     * PUT /admin/users/{user}
     */
    public function update(Request $request, User $user)
    {
        // Validate input, exclude current user from unique checks
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender'    => 'required|in:male,female',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'required|string|max:20|unique:users,phone,' . $user->id,
            'birthdate' => 'nullable|date',
            'user_type' => 'required|in:admin,employer,attendance',
        ]);

        // Update user record
        $user->update([
            'full_name' => $request->full_name,
            'gender'    => $request->gender,
            'username'  => $request->username,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'birthdate' => $request->birthdate,
            'user_type' => $request->user_type,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * 🔹 Delete a user
     * DELETE /admin/users/{user}
     */
    public function destroy(User $user)
    {
        // Delete user record
        $user->delete();

        return redirect()
            ->back()
            ->with('success', 'User deleted successfully!');
    }
}