<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    /**
     * Store a new asset assignment.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id'  => 'required|exists:users,id',
        ]);

        // Retrieve the selected asset
        $asset = Asset::findOrFail($request->asset_id);

        // Close any previous active assignment for this asset (if not returned yet)
        AssetAssignment::where('asset_id', $asset->id)
            ->whereNull('returned_date')
            ->update([
                'returned_date' => now()
            ]);

        // Create a new asset assignment record
        AssetAssignment::create([
            'asset_id'      => $asset->id,
            'user_id'       => $request->user_id,
            'assigned_date' => now(),
        ]);

        // Update asset status to assigned
        $asset->update([
            'status' => 'assigned'
        ]);

        return back()->with('success', 'Asset assigned successfully');
    }

    /**
     * Mark an asset as returned.
     */
    public function return(AssetAssignment $assignment)
    {
        // Prevent returning the same asset twice
        if ($assignment->returned_date) {
            return back()->with('error', 'Asset already returned');
        }

        // Update assignment with returned date
        $assignment->update([
            'returned_date' => now()
        ]);

        // Update related asset status to available
        $assignment->asset->update([
            'status' => 'available'
        ]);

        return back()->with('success', 'Asset returned successfully');
    }
}