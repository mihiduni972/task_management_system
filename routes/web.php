<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Login page
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Login submit
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ================= DASHBOARD =================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ================= TASK ROUTES =================

    // Tasks table page
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    // Add Task form page
    Route::get('/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    // Store task
    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    // 🔹 SHOW single task
    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');

    // 🔹 EDIT task form
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])
        ->name('tasks.edit');

    // 🔹 UPDATE task
    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update');

    // 🔹 DELETE task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy');

    // (existing status route – untouched)
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');

    // ================= ADMIN USERS =================

    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('admin.users.store');

    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.delete');

    // ================= PASSWORD =================

    Route::get('/change-password', [AuthController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('password.update');
});
