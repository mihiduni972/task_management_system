<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeLog; 
use App\Models\Task; 
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    /**
     * Display the current user's work logs.
     */
    public function myLogs()
    {
        // Fetch user's time logs with related task and project, paginated (6 per page)
        $timeLogs = TimeLog::with(['user', 'task.project'])
            ->where('user_id', auth()->id())
            ->orderBy('log_date', 'desc')
            ->paginate(6);

        return view('frontend.time-logs.my-logs', compact('timeLogs'));
    }

    /**
     * Store a new work log for the authenticated user.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'task_id'     => 'required|exists:tasks,id',
            'hours'       => 'required|integer|min:0|max:24',
            'minutes'     => 'required|integer|min:0|max:59',
            'log_date'    => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Convert hours + minutes into decimal format
        $decimalHours = $request->hours + ($request->minutes / 60);

        // Create a new time log record
        TimeLog::create([
            'task_id'     => $request->task_id,
            'user_id'     => Auth::id(),
            'hours'       => $decimalHours,
            'description' => $request->description,
            'log_date'    => $request->log_date,
        ]);

        return redirect()->back()->with('success', 'Time logged successfully!');
    }

    /**
     * Show the edit form for a specific work log.
     */
    public function edit($id)
    {
        // Fetch the log for the authenticated user only
        $log = TimeLog::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Fetch all tasks for selection in the edit form if needed
        $tasks = Task::all(); 

        return view('frontend.time-logs.edit', compact('log', 'tasks'));
    }

    /**
     * Update an existing work log.
     */
    public function update(Request $request, $id)
    {
        // Fetch the log for the authenticated user only
        $log = TimeLog::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validate request (task_id is intentionally commented out for updates)
        $request->validate([
            // 'task_id' => 'required|exists:tasks,id', // Optional: keep commented
            'hours'       => 'required|integer|min:0|max:24',
            'minutes'     => 'required|integer|min:0|max:59',
            'log_date'    => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Convert hours + minutes into decimal
        $decimalHours = $request->hours + ($request->minutes / 60);

        // Update log
        $log->update([
            // 'task_id'  => $request->task_id, // Optional: keep commented
            'hours'       => $decimalHours,
            'description' => $request->description,
            'log_date'    => $request->log_date,
        ]);

        return redirect()->back()->with('success', 'Work log updated successfully!');
    }

    /**
     * Delete a work log.
     */
    public function destroy($id)
    {
        // Fetch the log for the authenticated user only
        $log = TimeLog::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $log->delete();

        return redirect()->back()->with('success', 'Work log deleted successfully!');
    }
}