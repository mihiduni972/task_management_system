<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;

class ExportController extends Controller
{
    // ================= EMPLOYEES CSV =================
    public function exportEmployees()
    {
        $fileName = 'employees.csv';
        $employees = User::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($employees) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'User Type', 'Created At']);

            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->id,
                    $employee->full_name,
                    $employee->email,
                    $employee->user_type,
                    $employee->created_at ? $employee->created_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ================= TASKS CSV =================
    public function exportTasks()
    {
        $fileName = 'tasks.csv';
        $tasks = Task::with('assignedUser')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Assigned To', 'Status', 'Created At']);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->id,
                    $task->task,
                    $task->assignedUser ? $task->assignedUser->full_name : 'N/A',
                    $task->status,
                    $task->created_at ? $task->created_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ================= PROJECTS CSV =================
    public function exportProjects()
    {
        $fileName = 'projects.csv';
        $projects = Project::with('employers')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Project Name',
                'Client',
                'Assigned Employers',
                'Start Date',
                'End Date',
                'Status',
                'Progress %'
            ]);

            foreach ($projects as $project) {
                $assignedUsers = $project->employers->pluck('full_name')->implode(', ');

                fputcsv($file, [
                    $project->id,
                    $project->name,
                    $project->client_name,
                    $assignedUsers ?: 'N/A',
                    $project->start_date,
                    $project->end_date,
                    $project->status,
                    $project->progress_percentage . '%',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ================= DATABASE BACKUP =================
    public function backupDatabase()
{
    $dbHost = env('DB_HOST');
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');

    $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
    $filePath = storage_path($fileName);

    // ⚠️ VERY IMPORTANT (XAMPP PATH)
    $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump";

    $command = "\"{$mysqldumpPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$filePath}\"";

    exec($command, $output, $result);

    if ($result !== 0) {
        return back()->with('error', 'Database backup failed');
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}}
