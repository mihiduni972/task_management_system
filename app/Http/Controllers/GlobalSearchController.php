<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Leave;
use App\Models\Asset;

class GlobalSearchController extends Controller
{
    /**
     * Perform a global search across multiple modules.
     */
    public function search(Request $request)
    {
        // Get search query input
        $query = $request->input('q');

        // Redirect back if search query is empty
        if (!$query) {
            return redirect()->back()->with('error', 'Please enter something to search');
        }

        // Search employees by full name (only employer user type)
        $employees = User::where('user_type', 'employer')
            ->where('full_name', 'LIKE', "%{$query}%")
            ->get();

        // Search projects by project name
        $projects = Project::where('name', 'LIKE', "%{$query}%")->get();

        // Search tasks by task field (not title column)
        $tasks = Task::where('task', 'LIKE', "%{$query}%")->get();

        // Search clients by client name
        $clients = Client::where('name', 'LIKE', "%{$query}%")->get();

        // Search leave records by reason
        $leaves = Leave::where('reason', 'LIKE', "%{$query}%")->get();

        // Search assets by asset name
        $assets = Asset::where('asset_name', 'LIKE', "%{$query}%")->get();

        // Return search results view with all matched data
        return view('global-search.results', compact(
            'employees',
            'projects',
            'tasks',
            'clients',
            'leaves',
            'assets',
            'query'
        ));
    }
}