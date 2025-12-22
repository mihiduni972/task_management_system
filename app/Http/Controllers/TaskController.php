<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function create()
    {
        return view('frontend.employer.add-task');
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required',
            'status' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        Task::create([
            'user_id' => auth()->id(),
            'task_title' => $request->task,
            'description' => $request->sub_task,
            'status_id' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->back()->with('success', 'Task Added');
    }
}
