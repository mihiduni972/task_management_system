<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /* =========================
       SHOW LOGIN PAGE
    ========================= */
    public function showLogin()
    {
        return view('frontend.login.index');
    }

    /* =========================
       HANDLE LOGIN
    ========================= */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        return back()->with('error', 'Invalid credentials');
    }

    /* =========================
       REDIRECT BY ROLE
    ========================= */
    protected function redirectByRole($user)
    {
        switch ($user->user_type) {

            case 'admin':
                return redirect()->route('admin.users.index');

            case 'employer':
                return redirect()->route('tasks.index');

            case 'attendance_marker':
                return redirect()->route('attendance.mark');

            default:
                Auth::logout();
                return redirect()->route('login');
        }
    }

    /* =========================
       LOGOUT
    ========================= */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
