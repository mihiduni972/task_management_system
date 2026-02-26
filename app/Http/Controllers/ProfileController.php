<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user profile page.
     */
    public function index()
    {
        return view('frontend.profile.index');
    }

    /**
     * Update authenticated user's basic profile information.
     */
    public function update(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        // Update authenticated user's profile details
        auth()->user()->update([
            'name'     => $request->name,
            'username' => $request->username,
        ]);

        // Redirect back to profile page with success toast message
        return redirect()
            ->route('profile.index')
            ->with('toast', 'Profile updated successfully');
    }

    /**
     * Update authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        // Validate password and confirmation
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        // Update password after hashing
        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Redirect back to profile page with success toast message
        return redirect()
            ->route('profile.index')
            ->with('toast', 'Password updated successfully');
    }
}