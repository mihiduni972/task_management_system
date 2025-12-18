<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Store Admin / Employer
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'  => 'required|unique:users',
            'password'  => 'required|min:6',
            'user_type' => 'required|in:admin,employer',
        ]);

        User::create([
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        // ✅ SUCCESS POPUP
        return redirect()->back()
            ->with('success', 'User added successfully!');
    }
}
