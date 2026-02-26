<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetAssignment;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a list of assets with search and filter options.
     */
    public function index(Request $request)
    {
        // Eager load latest assignment with related user to prevent N+1 issue
        $query = Asset::with(['latestAssignment.user']);

        // Search by asset name or asset type
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('asset_name', 'like', '%' . $request->search . '%')
                  ->orWhere('asset_type', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by asset status (available / assigned)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get paginated asset list ordered by latest
        $assets = $query->latest()->paginate(5);

        // Get all available assets for assignment dropdown
        $allAvailableAssets = Asset::where('status', 'available')->get();

        // Get all employer users for assignment
        $users = User::where('user_type', 'employer')->get();

        return view('frontend.admin.assets.index', compact('assets', 'users', 'allAvailableAssets'));
    }

    /**
     * Store a new asset or update quantity if it already exists.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_type' => 'required',
            'quantity'   => 'required|integer|min:1',
        ]);

        // Check if asset with same name and type already exists
        $existingAsset = Asset::where('asset_name', $request->asset_name)
                              ->where('asset_type', $request->asset_type)
                              ->first();

        if ($existingAsset) {
            // Increase quantity if asset exists
            $existingAsset->quantity += $request->quantity;
            $existingAsset->save();
            $statusMessage = 'Asset quantity updated successfully!';
        } else {
            // Create new asset record
            Asset::create([
                'asset_name'  => $request->asset_name,
                'asset_type'  => $request->asset_type,
                'description' => $request->description,
                'quantity'    => $request->quantity,
                'status'      => 'available',
            ]);
            $statusMessage = 'New asset created successfully!';
        }

        return redirect()->route('assets.index')->with('success', $statusMessage);
    }

    /**
     * Assign an asset to a user (uses AssetAssignment table).
     */
    public function assign(Request $request)
    {
        // Validate assignment request
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id'  => 'required|exists:users,id',
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        // Close any previous active assignment for this asset
        AssetAssignment::where('asset_id', $asset->id)
            ->whereNull('returned_date')
            ->update([
                'returned_date' => now()
            ]);

        // Create new assignment record
        AssetAssignment::create([
            'asset_id'      => $asset->id,
            'user_id'       => $request->user_id,
            'assigned_date' => now(),
        ]);

        // Update asset status to assigned
        $asset->update([
            'status' => 'assigned'
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset assigned successfully!');
    }

    /**
     * Return an assigned asset and update its status.
     */
    public function returnAsset($id)
    {
        $asset = Asset::findOrFail($id);

        // Mark active assignment as returned
        AssetAssignment::where('asset_id', $asset->id)
            ->whereNull('returned_date')
            ->update([
                'returned_date' => now()
            ]);

        // Update asset status to available
        $asset->update([
            'status' => 'available'
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset returned successfully!');
    }

    /**
     * Update asset details.
     */
    public function update(Request $request, Asset $asset)
    {
        // Validate update request
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_type' => 'required',
            'quantity'   => 'required|integer|min:1',
        ]);

        // Update asset with request data
        $asset->update($request->all());

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully!');
    }

    /**
     * Delete an asset from the system.
     */
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        // Delete asset record
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully!');
    }
}