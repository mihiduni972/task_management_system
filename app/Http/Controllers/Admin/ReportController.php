<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   public function index()
{
    $selectedProject = request('project_id');
    $from = request('from');
    $to = request('to');

    $taskQuery = Task::query();

    // 🔹 Project Filter
    if ($selectedProject) {
        $taskQuery->where('project_id', $selectedProject);
    }

    // 🔹 Date Filter
    if ($from && $to) {
        $taskQuery->whereBetween('created_at', [$from, $to]);
    }

    /* ================= BASIC STATS ================= */

    $totalEmployees = User::where('user_type', 'employer')->count();

    $totalTasks = $taskQuery->count();

    $completedTasks = (clone $taskQuery)
        ->where('status', 'completed')
        ->count();

    $pendingTasks = $totalTasks - $completedTasks;

    $taskCompletionRate = $totalTasks > 0
        ? round(($completedTasks / $totalTasks) * 100, 1)
        : 0;

    /* ================= PROJECT SUMMARY ================= */

    $projectSummary = Project::withCount(['tasks' => function ($query) use ($from, $to) {
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }])
    ->withCount(['tasks as completed_tasks_count' => function ($query) use ($from, $to) {
        $query->where('status', 'completed');
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }])
    ->get();

    /* ================= EMPLOYEE PERFORMANCE ================= */

    $employeePerformance = User::where('user_type', 'employer')
        ->withCount(['tasks as completed_task_count' => function ($query) use ($selectedProject, $from, $to) {

            $query->where('status', 'completed');

            if ($selectedProject) {
                $query->where('project_id', $selectedProject);
            }

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }])
        ->orderByDesc('completed_task_count')
        ->limit(5)
        ->get();

    $projects = Project::all();

    $totalProjects = Project::count();

return view('frontend.admin.reports.index', compact(
    'totalEmployees',
    'totalTasks',
    'completedTasks',
    'pendingTasks',
    'taskCompletionRate',
    'totalProjects',   
    'projectSummary',
    'employeePerformance',
    'projects',
    'selectedProject',
    'from',
    'to'
));
}
}