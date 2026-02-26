<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // ===================== Admin Methods =====================

    /**
     * Admin - List all projects with related clients, employers, tasks, and milestones.
     */
    public function index()
    {
        // Fetch projects with related data and paginate (7 per page)
        $projects = Project::with(['client', 'employers', 'tasks', 'milestones'])
                            ->latest()
                            ->paginate(7);

        // Fetch all employers for dropdown assignment
        $employers = User::where('user_type', 'employer')->get();

        return view('frontend.admin.projects.index', compact('projects', 'employers'));
    }

    /**
     * Admin - Store a new project.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'client_id'   => 'required|exists:clients,id', // Foreign key validation
            'description' => 'nullable|string',
            'status'      => 'required|in:ongoing,completed,on-hold,pending',
            'employers'   => 'nullable|array'
        ]);

        // Automatically assign client name for consistency
        $client = Client::find($data['client_id']);
        $data['client_name'] = $client ? $client->name : null;

        // Create project with the current authenticated user as creator
        $project = Project::create(array_merge($data, [
            'created_by' => Auth::id(),
        ]));

        // Assign selected employers to the project
        if ($request->has('employers')) {
            $project->employers()->sync($request->employers);
        }

        return redirect()->route('admin.projects.index')
                         ->with('success', 'Project created successfully.');
    }

    /**
     * Admin - Show method redirects to index (no separate view).
     */
    public function show(Project $project)
    {
        return redirect()->route('admin.projects.index');
    }

    /**
     * Admin - Update an existing project.
     */
    public function update(Request $request, Project $project)
    {
        // Validate incoming request
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'client_id'   => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'status'      => 'required|in:ongoing,completed,on-hold,pending',
            'employers'   => 'nullable|array'
        ]);

        // Update client_name automatically
        $client = Client::find($data['client_id']);
        $data['client_name'] = $client ? $client->name : null;

        // Update project details
        $project->update($data);

        // Update employer assignments
        $project->employers()->sync($request->employers ?? []);

        return redirect()->route('admin.projects.index')
                         ->with('success', 'Project updated successfully.');
    }

    /**
     * Admin - Delete a project.
     */
    public function destroy(Project $project)
    {
        // Detach all employer relationships before deleting
        $project->employers()->detach();
        $project->delete();

        return redirect()->route('admin.projects.index')
                         ->with('success', 'Project deleted successfully.');
    }

    // ===================== Employer Methods =====================

    /**
     * Employer - List all projects assigned to the logged-in employer.
     * Uses pagination (7 per page) for easier browsing.
     */
    public function myProjects()
    {
        $projects = Auth::user()
                        ->projects()
                        ->with(['client', 'tasks', 'milestones'])
                        ->latest()
                        ->paginate(7)
                        ->withQueryString(); // Keep filters/search query intact

        return view('frontend.employer.projects.index', compact('projects'));
    }

    /**
     * Employer - View tasks and milestones of a specific project.
     * Checks if the logged-in user is assigned to this project.
     */
    public function myTasks(Project $project)
    {
        // Abort if the user is not part of the project
        if (!$project->employers->contains(Auth::id())) {
            abort(403, 'Unauthorized access to this project.');
        }

        // Paginate tasks assigned to this user (1 per page)
        $tasks = $project->tasks()
                         ->where('user_id', Auth::id())
                         ->latest()
                         ->paginate(1, ['*'], 'tasks_page')
                         ->withQueryString();

        // Paginate milestones for this project (1 per page)
        $milestones = $project->milestones()
                              ->latest()
                              ->paginate(1, ['*'], 'milestones_page')
                              ->withQueryString();

        return view('frontend.employer.projects.tasks', compact('project', 'tasks', 'milestones'));
    }
}