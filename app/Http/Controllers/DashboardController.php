<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show employer dashboard (Add Task page)
     */
    public function index()
    {
        return view('frontend.employer.add-task');
    }
}
