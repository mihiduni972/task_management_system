<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /* =====================================================
     * EMPLOYER – VIEW OWN ATTENDANCE
     * ===================================================== */
    public function employerIndex()
    {
        abort_if(auth()->user()->user_type !== 'employer', 403);

        $attendances = Attendance::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->paginate(7)
            ->withQueryString();
        
        $employers = User::where('user_type', 'employer')
            ->orderBy('full_name')
            ->get();

        return view('frontend.attendance.employer.index', compact('attendances', 'employers'));
    }

    /* =====================================================
     * EMPLOYER – SHOW SINGLE ATTENDANCE
     * ===================================================== */
    public function employerShow(Attendance $attendance)
    {
        return response()->json([
            'success'   => true,
            'date'      => Carbon::parse($attendance->date)->format('Y-m-d'),
            'status'    => ucfirst($attendance->status),
            'check_in'  => $attendance->check_in ?? '-',
            'check_out' => $attendance->check_out ?? '-',
            'hours'     => $attendance->worked_minutes
                ? number_format($attendance->worked_minutes / 60, 2) . ' hrs'
                : '-',
        ]);
    }

    /* =====================================================
     * EMPLOYER – EDIT ATTENDANCE
     * ===================================================== */
    public function employerEdit(Attendance $attendance)
    {
        return response()->json([
            'id'        => $attendance->id,
            'user_id'   => $attendance->user_id, 
            'date'      => $attendance->date,
            'status'    => $attendance->status,
            'check_in'  => $attendance->check_in,
            'check_out' => $attendance->check_out,
        ]);
    }

    /* =====================================================
     * EMPLOYER – STORE / UPDATE ATTENDANCE
     * ===================================================== */
    public function employerStore(Request $request)
    {
        abort_if(auth()->user()->user_type !== 'employer', 403);

        $request->validate([
            'user_id'   => 'required|exists:users,id', 
            'date'      => 'required|date',
            'status'    => 'required|in:present,absent,late',
            'check_in'  => 'nullable',
            'check_out' => 'nullable',
        ]);

        $workedMinutes = null;
        if ($request->status !== 'absent' && $request->check_in && $request->check_out) {
            $workedMinutes = Carbon::parse($request->check_in)
                ->diffInMinutes(Carbon::parse($request->check_out));
        }

        Attendance::updateOrCreate(
            ['id' => $request->attendance_id],
            [
                'user_id'        => $request->user_id,
                'date'           => $request->date,
                'status'         => $request->status,
                'check_in'       => $request->status === 'absent' ? null : $request->check_in,
                'check_out'      => $request->status === 'absent' ? null : $request->check_out,
                'worked_minutes' => $workedMinutes,
                'marked_by'      => auth()->id(),
            ]
        );
        return response()->json([
            'success' => true,
            'message' => $request->attendance_id ? 'Attendance updated successfully' : 'Attendance added successfully'
        ]);
    }

    /* =====================================================
     * EMPLOYER – DELETE ATTENDANCE
     * ===================================================== */
    public function employerDestroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully'
        ]);
    }

    /* =====================================================
     * ADMIN – VIEW ALL ATTENDANCE
     * ===================================================== */
    public function adminIndex(Request $request)
    {
        $query = Attendance::with(['user', 'marker']);

        if ($request->filled('employer_id')) {
            $query->where('user_id', $request->employer_id);
        }

        $attendances = $query->orderBy('date', 'desc')
                             ->paginate(7)
                             ->withQueryString();

        $employers = User::where('user_type', 'employer')
            ->orderBy('full_name')
            ->get();

        return view('frontend.admin.attendance.index', compact('attendances', 'employers'));
    }

    /* =====================================================
     * ADMIN – MONTHLY REPORT
     * ===================================================== */
    public function monthlyReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
            $end   = now()->endOfMonth();
        }

        $users = User::where('user_type', 'employer')
            ->with(['attendances' => function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            }])
            ->get();

        return view('frontend.attendance.admin.report', compact('users', 'month'));
    }
}