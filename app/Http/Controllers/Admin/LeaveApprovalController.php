<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller
{
    /* ===============================
       LIST ALL EMPLOYER LEAVES
    =============================== */
    public function index()
    {
        $leaves = Leave::with('user')
            ->latest()
            ->paginate(7);
        return view('frontend.admin.leaves.index', compact('leaves'));
    }

    /* ===============================
       APPROVE LEAVE
    =============================== */
    public function approve($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Leave approved successfully'
        ]);
    }

    /* ===============================
       REJECT LEAVE
    =============================== */
    public function reject($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Leave rejected successfully'
        ]);
    }
}
