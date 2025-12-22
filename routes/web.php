<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Login page (GET)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// 🔹 Login submit (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// 🔹 Logout (POST)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 Protected routes (only after login)
Route::middleware('auth')->group(function () {

    // 🔹 Employer Dashboard (Login after page – 3rd page)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // 🔹 Tasks (Employer)
    Route::get('/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    Route::post('/tasks/store', [TaskController::class, 'store'])
        ->name('tasks.store');

    // 🔹 Change Password
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('password.update');

    // 🔹 Admin – Create Users
    Route::post('/users/store', [UserController::class, 'store'])
        ->name('users.store');
});
