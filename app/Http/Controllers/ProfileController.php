<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('frontend.profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        auth()->user()->update([
            'name'     => $request->name,
            'username' => $request->username,
        ]);

        // ✅ profile update unama profile page ekema innawa (toast witharai)
        return redirect()
            ->route('profile.index')
            ->with('toast', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // ✅ password update unama USERS page ekata redirect
        return redirect()
            ->route('admin.users')   // 🔥 CORRECT ROUTE NAME
            ->with('toast', 'Password updated successfully');
    }
}
