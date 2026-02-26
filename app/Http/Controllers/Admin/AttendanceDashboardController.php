<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get selected date from request or default to today
        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        // Get selected month and year (fallback to selected date values)
        $selectedMonth = $request->month ?? $selectedDate->month;
        $selectedYear  = $request->year ?? $selectedDate->year;

        // ======================================
        // Summary Cards (Based on Selected Date)
        // ======================================

        // Total number of employees (user_type = employer)
        $totalEmployees = User::whereRaw('LOWER(user_type) = ?', ['employer'])->count();

        // Count of employees marked as present on selected date
        $presentToday = Attendance::whereDate('date', $selectedDate)
            ->where('status', 'present')
            ->count();

        // Count of employees marked as absent on selected date
        $absentToday = Attendance::whereDate('date', $selectedDate)
            ->where('status', 'absent')
            ->count();

        // Count of employees marked as late on selected date
        $lateToday = Attendance::whereDate('date', $selectedDate)
            ->where('status', 'late')
            ->count();

        // ======================================
        // Last 7 Days Attendance (Daily Chart)
        // ======================================

        // Generate last 7 days collection based on selected date
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $last7Days->push($selectedDate->copy()->subDays($i));
        }

        // Format labels for chart (Mon, Tue, Wed, etc.)
        $last7DaysLabels = $last7Days
            ->map(fn($d) => $d->format('D'))
            ->toArray();

        // Get daily present counts for last 7 days
        $onTimeLast7DaysData = $last7Days
            ->map(fn($d) =>
                Attendance::whereDate('date', $d)
                    ->where('status', 'present')
                    ->count()
            )->toArray();

        // Get daily late counts for last 7 days
        $lateLast7DaysData = $last7Days
            ->map(fn($d) =>
                Attendance::whereDate('date', $d)
                    ->where('status', 'late')
                    ->count()
            )->toArray();

        // Get daily absent counts for last 7 days
        $absentLast7DaysData = $last7Days
            ->map(fn($d) =>
                Attendance::whereDate('date', $d)
                    ->where('status', 'absent')
                    ->count()
            )->toArray();

        // ======================================
        // Monthly Summary (Selected Month/Year)
        // ======================================

        // Total present count for selected month
        $monthlyOnTime = Attendance::whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->where('status', 'present')
            ->count();

        // Total late count for selected month
        $monthlyLate = Attendance::whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->where('status', 'late')
            ->count();

        // Total absent count for selected month
        $monthlyAbsent = Attendance::whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->where('status', 'absent')
            ->count();

        // ======================================
        // Return View with All Dashboard Data
        // ======================================
        return view('frontend.admin.attendance.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'absentToday',
            'lateToday',
            'last7DaysLabels',
            'onTimeLast7DaysData',
            'lateLast7DaysData',
            'absentLast7DaysData',
            'monthlyOnTime',
            'monthlyLate',
            'monthlyAbsent',
            'selectedDate',
            'selectedMonth',
            'selectedYear'
        ));
    }
}