<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /**
     * Store a newly created milestone.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title'      => 'required|string|max:255',
            'progress'   => 'required|integer|min:0|max:100',
        ]);

        // Create milestone and set status based on progress value
        Milestone::create([
            'project_id' => $request->project_id,
            'title'      => $request->title,
            'progress'   => $request->progress,
            // If progress is 100%, mark as completed; otherwise mark as ongoing
            'status'     => ($request->progress == 100) ? 'completed' : 'ongoing',
        ]);

        return back()->with('success', 'Milestone created successfully!');
    }

    /**
     * Update the progress (and optionally title) of an existing milestone.
     */
    public function updateProgress(Request $request, $id)
    {
        // Validate incoming request data
        $request->validate([
            'title'    => 'nullable|string|max:255', // Title may come from modal
            'progress' => 'required|integer|min:0|max:100',
        ]);

        // Find milestone by ID
        $milestone = Milestone::findOrFail($id);

        // Update title only if provided
        if ($request->has('title')) {
            $milestone->title = $request->title;
        }

        // Update progress value
        $milestone->progress = $request->progress;

        // Automatically update status based on progress value
        $milestone->status = ($request->progress == 100) ? 'completed' : 'ongoing';

        $milestone->save();

        return back()->with('success', 'Milestone updated successfully!');
    }

    /**
     * Delete an existing milestone.
     */
    public function destroy($id)
    {
        $milestone = Milestone::findOrFail($id);
        $milestone->delete();

        return back()->with('success', 'Milestone deleted successfully!');
    }
}