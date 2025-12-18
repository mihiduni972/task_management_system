<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Login page
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// 🔹 Login submit
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// 🔹 Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 Protected routes (login required)
Route::middleware(['auth'])->group(function () {

    // ✅ Admin / Employer create (no dashboard redirects)
    Route::post('/users/store', [UserController::class, 'store'])
        ->name('users.store');
});
