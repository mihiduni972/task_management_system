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
            $request->session()->regenerate();

            return $this->redirectByRole();
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

        return $this->redirectByRole()
            ->with('success', 'Password Updated Successfully');
    }

    /**
     * 🔹 Role based redirect (ADMIN / EMPLOYER)
     */
    private function redirectByRole()
    {
        $user = auth()->user();

        // SAFETY CHECK
        if (!$user) {
            return redirect()->route('login');
        }

        // ADMIN
        if ($user->user_type === 'admin') {
            return redirect()->route('dashboard')
                ->with('success', 'Welcome Admin');
        }

        // EMPLOYER
        if ($user->user_type === 'employer') {
            return redirect()->route('tasks.index')
                ->with('success', 'Welcome Employer');
        }

        // FALLBACK (unknown role)
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Unauthorized user type');
    }
}
