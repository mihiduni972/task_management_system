<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 🔹 Show users list (Admin page)
     * GET /admin/users
     * - Search by username
     * - Pagination (8 per page)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 🔍 Search by username
        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
        }

        // 📄 Pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(8);

        return view('frontend.users.index', compact('users'));
    }

    /**
     * 🔹 Store Admin / Employer
     * POST /admin/users
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'birthdate' => 'nullable|date',
            'password'  => 'required|min:6',
            'user_type' => 'required|in:admin,employer',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
            'birthdate' => $request->birthdate,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User added successfully!');
    }

    /**
     * 🔹 Edit user form
     * GET /admin/users/{user}/edit
     */
    public function edit(User $user)
    {
        return view('frontend.users.edit', compact('user'));
    }

    /**
     * 🔹 Update user
     * PUT /admin/users/{user}
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'birthdate' => 'nullable|date',
            'user_type' => 'required|in:admin,employer',
        ]);

        $user->update([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
            'birthdate' => $request->birthdate,
            'user_type' => $request->user_type,
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully!');
    }

    /**
     * 🔹 Delete user
     * DELETE /admin/users/{user}
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()
            ->with('success', 'User deleted successfully!');
    }
}