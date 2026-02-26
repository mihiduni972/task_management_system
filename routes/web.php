<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\Admin\LeaveApprovalController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\Admin\AdminTimeLogController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Admin\AttendanceDashboardController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (ALL LOGGED USERS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* ================= DASHBOARD ================= */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ================= GLOBAL SEARCH ================= */
Route::get('/global-search', [GlobalSearchController::class, 'search'])
    ->name('global.search');


    /* ================= TASKS ================= */
    Route::resource('tasks', TaskController::class)->except(['create', 'edit']);
    
    
/* ================= TIME TRACKING ================= */
// Store log
Route::post('/time-logs/store', [TimeLogController::class, 'store'])->name('time-logs.store');

// List logs
Route::get('/my-time-logs', [TimeLogController::class, 'myLogs'])->name('time-logs.my-logs');

// Show/Edit specific log (This was missing!)
Route::get('/time-logs/{id}/edit', [TimeLogController::class, 'edit'])->name('time-logs.edit');

// Update log
Route::put('/time-logs/{id}', [TimeLogController::class, 'update'])->name('time-logs.update');

// Delete log
Route::delete('/time-logs/{id}', [TimeLogController::class, 'destroy'])->name('time-logs.destroy');

    /* ================= PROFILE ================= */
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /* ================= EMPLOYER – MY ATTENDANCE ================= */
    Route::prefix('my-attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'employerIndex'])->name('attendance.employer.index');
        Route::post('/store', [AttendanceController::class, 'employerStore'])->name('attendance.employer.store');
        Route::get('/{attendance}', [AttendanceController::class, 'employerShow'])->name('attendance.employer.show');
        Route::get('/{attendance}/edit', [AttendanceController::class, 'employerEdit'])->name('attendance.employer.edit');
        Route::delete('/{attendance}', [AttendanceController::class, 'employerDestroy'])->name('attendance.employer.destroy');
    });

   /* ================= EMPLOYER – LEAVES ================= */
    Route::prefix('employer/leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/', [LeaveController::class, 'store'])->name('leaves.store');
        Route::get('/table', [LeaveController::class, 'table'])->name('employer.leaves.table'); // Table route එක උඩින් තියන්න
        
     
        Route::get('/{id}', [LeaveController::class, 'show'])->name('employer.leaves.show');
        
        Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('employer.leaves.edit');
        Route::put('/{id}', [LeaveController::class, 'update'])->name('employer.leaves.update');
        Route::delete('/{id}', [LeaveController::class, 'destroy'])->name('employer.leaves.destroy');
    });

    /* ================= EMPLOYER – PROJECTS & TASKS ================= */
    Route::get('/my-projects', [ProjectController::class, 'myProjects'])->name('employer.projects');
    Route::get('/my-projects/{project}/tasks', [ProjectController::class, 'myTasks'])->name('employer.projects.tasks');
    Route::patch('/employer/tasks/{task}/update', [TaskController::class, 'updateStatus'])
        ->name('employer.tasks.update');

    /* ================= MILESTONES ================= */
    Route::post('/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
    Route::post('/milestones/{id}/update-progress', [MilestoneController::class, 'updateProgress'])
        ->name('milestones.update_progress');
    Route::delete('/milestones/{id}', [MilestoneController::class, 'destroy'])
        ->name('milestones.destroy');

  /* ================= ASSET MANAGEMENT ================= */

// Main Asset List
Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');

// Store New Asset (From Add Modal)
Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');

// Update Asset (From Edit Modal)
Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');

// Delete Asset
Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');

/* --- ASSET ASSIGNMENT & RETURN --- */

// Assign Asset to User 
Route::post('/assets/assign', [AssetController::class, 'assign'])->name('assets.assign');

// Return Assigned Asset 
Route::post('/assets/return/{id}', [AssetController::class, 'returnAsset'])->name('assets.return');
    /* ================= NOTIFICATIONS ================= */
    Route::post('/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    )->name('notifications.markAsRead');

    Route::post('/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    )->name('notifications.markAllAsRead');
});
/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {

    /* ================= USERS ================= */
    Route::resource('users', UserController::class)->names([
        'index'   => 'admin.users.index',
        'store'   => 'admin.users.store',
        'show'    => 'admin.users.show',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    /* ================= Attendance Analytics Dashboard ================= */
    // NEW: Attendance Dashboard link
    Route::get('/attendance-dashboard', [AttendanceDashboardController::class, 'index'])
        ->name('admin.attendance.dashboard');

    /* ================= ATTENDANCE ================= */
    Route::get('/attendance', [AttendanceController::class, 'adminIndex'])
        ->name('admin.attendance.index');
    Route::get('/attendance/report', [AttendanceController::class, 'monthlyReport'])
        ->name('admin.attendance.report');

    /* ================= TASKS ================= */
    Route::get('/tasks', [TaskController::class, 'adminIndex'])
        ->name('admin.tasks.index');

    /* ================= LEAVES ================= */
    Route::get('/leaves', [LeaveApprovalController::class, 'index'])
        ->name('admin.leaves.index');
    Route::post('/leaves/{id}/approve', [LeaveApprovalController::class, 'approve'])
        ->name('admin.leaves.approve');
    Route::post('/leaves/{id}/reject', [LeaveApprovalController::class, 'reject'])
        ->name('admin.leaves.reject');

    /* ================= PROJECTS ================= */
    Route::resource('projects', ProjectController::class)->names([
        'index'   => 'admin.projects.index',
        'create'  => 'admin.projects.create',
        'store'   => 'admin.projects.store',
        'edit'    => 'admin.projects.edit',
        'update'  => 'admin.projects.update',
        'destroy' => 'admin.projects.destroy',
    ]);

    Route::get('projects/{project}/tasks', [ProjectController::class, 'tasks'])
        ->name('admin.projects.tasks');

    /* ================= CLIENTS ================= */
    Route::resource('clients', ClientController::class)->names([
        'index'   => 'admin.clients.index',
        'create'  => 'admin.clients.create',
        'store'   => 'admin.clients.store',
        'show'    => 'admin.clients.show',
        'edit'    => 'admin.clients.edit',
        'update'  => 'admin.clients.update',
        'destroy' => 'admin.clients.destroy',
    ]);

    /* ================= REPORTS ================= */
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('admin.reports.index');

    /* ================= WORK LOGS MANAGEMENT ================= */
    Route::get('/time-logs', [AdminTimeLogController::class, 'index'])
        ->name('admin.time-logs.index');

    /* ================= EXPORTS ================= */
    Route::get('/export/employees', [ExportController::class, 'exportEmployees'])
        ->name('admin.export.employees');
    Route::get('/export/tasks', [ExportController::class, 'exportTasks'])
        ->name('admin.export.tasks');
    Route::get('/export/projects', [ExportController::class, 'exportProjects'])
        ->name('admin.export.projects');

    /* ================= DATABASE BACKUP ================= */
    Route::get('/backup/database', [ExportController::class, 'backupDatabase'])
        ->name('admin.backup.database');

});