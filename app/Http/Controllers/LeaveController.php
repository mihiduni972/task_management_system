<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;

class LeaveController extends Controller
{
    /* ===============================
        LIST MY LEAVES
    =============================== */
    public function index()
    {
        
        $leaves = Leave::where('user_id', auth()->id())
            ->latest()
            ->paginate(6); 

        return view('frontend.leaves.index', compact('leaves'));
    }

    /* =========================================
        SHOW (FETCH SINGLE LEAVE FOR VIEW MODAL)
        ========================================= */
    public function show($id)
    {
        $leave = Leave::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'from_date' => $leave->from_date,
            'to_date'   => $leave->to_date,
            'type'      => ucfirst($leave->type),
            'reason'    => $leave->reason ?? 'No reason provided',
            'status'    => ucfirst($leave->status),
        ]);
    }

    /* ===============================
        STORE (ADD LEAVE)
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'type'      => 'required|string',
            'reason'    => 'nullable|string',
        ]);

        Leave::create([
            'user_id'   => auth()->id(),
            'from_date' => $request->from_date,
            'to_date'   => $request->to_date,
            'type'      => $request->type,
            'reason'    => $request->reason,
            'status'    => 'pending',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Leave applied successfully'
        ]);
    }

    /* ===============================
        EDIT (FETCH SINGLE LEAVE)
        – used for edit modal
    =============================== */
    public function edit($id)
    {
        $leave = Leave::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

       
        return response()->json([
            'id'        => $leave->id,
            'from_date' => \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d'),
            'to_date'   => \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d'),
            'type'      => $leave->type,
            'reason'    => $leave->reason,
        ]);
    }

    /* ===============================
        UPDATE LEAVE
    =============================== */
    public function update(Request $request, $id)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'type'      => 'required|string',
            'reason'    => 'nullable|string',
        ]);

        $leave = Leave::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $leave->update([
            'from_date' => $request->from_date,
            'to_date'   => $request->to_date,
            'type'      => $request->type,
            'reason'    => $request->reason,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Leave updated successfully'
        ]);
    }

    /* ===============================
        DELETE LEAVE
    =============================== */
    public function destroy($id)
    {
        $leave = Leave::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $leave->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Leave deleted successfully'
        ]);
    }

    /* ===============================
        TABLE PARTIAL (AJAX REFRESH)
    =============================== */
    public function table()
    {
        $leaves = Leave::where('user_id', auth()->id())
            ->latest()
            ->paginate(6); 

        return view('frontend.leaves.partials.table', compact('leaves'));
    }
}