<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * EMPLOYER – View tasks assigned to the logged-in employer.
     */
    public function index()
    {
        // Fetch tasks with related project and time logs, paginated (6 per page)
        $tasks = Task::with(['project', 'timeLogs'])
                     ->where('user_id', Auth::id())
                     ->latest()
                     ->paginate(6);

        // Fetch all projects for filtering or assignment dropdown
        $projects = Project::all(); 

        return view('frontend.tasks.index', compact('tasks', 'projects'));
    }

    /**
     * ADMIN – View all tasks for employers with optional filters.
     */
    public function adminIndex(Request $request)
    {
        // Restrict access to admin only
        abort_if(Auth::user()->user_type !== 'admin', 403);

        // Fetch all employers and projects for dropdowns
        $employers = User::where('user_type', 'employer')->get();
        $projects = Project::all();

        // Base query for tasks with eager loading
        $query = Task::with(['user', 'project', 'timeLogs', 'assignedUser'])->latest();

        // Filter by employer
        if ($request->filled('employer_id')) {
            $query->where('user_id', $request->employer_id);
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Paginate results (7 per page)
        $tasks = $query->paginate(7);

        return view('frontend.admin.tasks.index', compact('tasks', 'employers', 'projects'));
    }

    /**
     * Store a new task.
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'task'        => 'required|string|max:255',
            'sub_task'    => 'nullable|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date',
            'status'      => 'required|in:Pending,Completed',
            'assigned_to' => 'nullable|exists:users,id', // optional for admin
        ]);

        // Determine who the task should be assigned to
        $assignedTo = Auth::user()->user_type === 'admin'
            ? $request->assigned_to // admin can assign manually
            : Auth::id();           // employer auto-assign to self

        // Create task record
        $task = Task::create([
            'project_id'  => $request->project_id,
            'user_id'     => Auth::id(),   // task owner
            'task'        => $request->task,
            'sub_task'    => $request->sub_task,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'status'      => $request->status,
            'assigned_to' => $assignedTo,
        ]);

        // Notify the assigned user if exists
        if ($task->assignedUser) {
            $task->assignedUser->notify(new TaskAssignedNotification($task));
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    /**
     * Show a single task (AJAX / JSON response)
     */
    public function show(Task $task)
    {
        return response()->json([
            'id'           => $task->id,
            'project_id'   => $task->project_id,
            'task'         => $task->task,
            'sub_task'     => $task->sub_task,
            'start_date'   => $task->start_date,
            'end_date'     => $task->end_date,
            'status'       => $task->status,
            'assigned_to'  => $task->assignedUser ? $task->assignedUser->full_name : 'N/A',
        ]);
    }

    /**
     * Update an existing task.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request
        $request->validate([
            'project_id'  => 'nullable|exists:projects,id',
            'task'        => 'nullable|string|max:255',
            'sub_task'    => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'status'      => 'required|in:Pending,Completed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Find task by ID
        $task = Task::findOrFail($id);

        // Update task with only allowed fields
        $task->update($request->only([
            'project_id', 'task', 'sub_task',
            'start_date', 'end_date',
            'status', 'assigned_to'
        ]));

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task successfully updated'
            ]);
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Task successfully updated');
    }

    /**
     * Remove a task.
     */
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Task successfully deleted');
    }
}