<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTimeLogController extends Controller
{
    /**
     * Display a listing of all employee time logs.
     */
    public function index(Request $request)
    {
        // Create base query with relationships (task -> project and user)
        $query = TimeLog::with(['task.project', 'user']);

        // Filter by selected employee (if user_id is provided)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by selected date (if date is provided)
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Order by latest created records and paginate (5 per page)
        $timeLogs = $query->latest('created_at')->paginate(5);

        // Get all users with employer role
        $employees = User::where('user_type', 'employer')->get();

        // Return view with time logs and employee list
        return view('frontend.admin.time-logs.index', compact('timeLogs', 'employees'));
    }
}