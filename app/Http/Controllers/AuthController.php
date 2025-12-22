<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        return view('frontend.login.index');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {

            // 🔐 Session fixation protection
            $request->session()->regenerate();

            // ✅ After login go to dashboard (3rd page)
            return redirect()->route('dashboard')
                ->with('success', 'Login Successfully');
        }

        return back()->with('error', 'Invalid Username or Password');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logged out successfully');
    }

    /**
     * Show change password page
     */
    public function showChangePassword()
    {
        return view('frontend.change-password');
    }

    /**
     * Update password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password Updated Successfully');
    }
}
