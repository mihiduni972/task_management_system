<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 🔐 Safety check
        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ ADMIN → Frontend Admin Dashboard
        if ($user->user_type === 'admin') {
            return view('frontend.admin.dashboard');
        }

        // ✅ EMPLOYER → Task List (direct)
        if ($user->user_type === 'employer') {
            return redirect()->route('tasks.index');
        }

        // ❌ Unknown role → logout
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Unauthorized access');
    }
}
