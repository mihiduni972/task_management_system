<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

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
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware('can:isAdmin')
        ->group(function () {

            Route::get('/users', [UserController::class, 'index'])
                ->name('admin.users');

            Route::post('/users', [UserController::class, 'store'])
                ->name('admin.users.store');

            Route::get('/users/{user}', [UserController::class, 'show'])
                ->name('admin.users.show');

            Route::get('/users/{user}/edit', [UserController::class, 'edit'])
                ->name('admin.users.edit');

            Route::put('/users/{user}', [UserController::class, 'update'])
                ->name('admin.users.update');

            Route::delete('/users/{user}', [UserController::class, 'destroy'])
                ->name('admin.users.destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | TASKS (ADMIN + EMPLOYER)
    |--------------------------------------------------------------------------
    */
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    Route::get('/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');


    /*
    |--------------------------------------------------------------------------
    | PROFILE (ADMIN + EMPLOYER)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::post('/profile/update', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
});
