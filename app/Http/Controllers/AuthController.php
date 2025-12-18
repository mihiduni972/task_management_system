<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            // ✅ SUCCESS MESSAGE (THIS WAS MISSING)
            return redirect()->back()
                ->with('success', 'Login Successfully');
        }

        // ❌ Only when login fails
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
}
