<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * EMPLOYER – Task list page
     */
public function index()
{
    $tasks = Task::all();

    return view('frontend.tasks.index', compact('tasks'));
}

}