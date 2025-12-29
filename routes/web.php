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

    /*
    |----------------------------------------------------------------------
    | DASHBOARD
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    /*
    |----------------------------------------------------------------------
    | ADMIN ROUTES
    |----------------------------------------------------------------------
    */

    // ✅ Admin - Users management (list)
    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users');

    // Create user
    Route::post('/admin/users', [UserController::class, 'store'])
        ->name('admin.users.store');

    // 🔹 Edit user (form)
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.users.edit');

    // 🔹 Update user
    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->name('admin.users.update');

    // Delete user
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.delete');


    /*
    |----------------------------------------------------------------------
    | EMPLOYER ROUTES
    |----------------------------------------------------------------------
    */

    // Task list
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    // Create task page
    Route::get('/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    // Store task
    Route::post('/tasks/store', [TaskController::class, 'store'])
        ->name('tasks.store');

    // Update task status
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');


    /*
    |----------------------------------------------------------------------
    | COMMON ROUTES (ADMIN + EMPLOYER)
    |----------------------------------------------------------------------
    */

    // Change Password
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('password.update');
});
