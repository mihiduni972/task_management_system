@extends('frontend.layouts.app')

@section('content')

{{-- ================= PAGE HEADER & FILTER SECTION (Outside the card) ================= --}}
<div class="users-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div class="header-left">
        <h2 style="font-size: 24px; font-weight: 600;">All Attendance</h2>
    </div>

        <style>
        
    .pagination-wrapper nav div:first-child,
    .pagination-wrapper nav p {
        display: none !important;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 25px;
    }

    .pagination {
        display: flex !important;
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 0 !important;
    }

    .pagination li a, 
    .pagination li span {
        min-width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0 !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-left: -1px;
    }

    .pagination li.active span {
        background: #000000 !important;
        color: #ffffff !important;
        border-color: #000000 !important;
    }

    .pagination li a:hover {
        background-color: #f3f4f6 !important;
        color: #000000 !important;
    }
        </style>

    {{-- EMPLOYER FILTER DROPDOWN --}}
    <div class="header-right">
        <form method="GET" action="{{ route('admin.attendance.index') }}" id="attendanceFilterForm">
            <select name="employer_id" 
                    onchange="this.form.submit()" 
                    class="form-select" 
                    style="width: 250px; border-radius: 8px; border: 1px solid #ddd; padding: 8px;">
                <option value="">Select Employer</option>
                @foreach($employers as $employer)
                    <option value="{{ $employer->id }}" 
                        {{ request('employer_id') == $employer->id ? 'selected' : '' }}>
                        {{ $employer->full_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

    {{-- TABLE WRAPPER --}}
    <div class="table-wrapper" style="padding: 20px;">
        <table class="table custom-table admin-attendance-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($attendances as $a)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $a->user?->profile_display_name ?? 'N/A' }}</td>
                        <td>{{ $a->date }}</td>
                        <td>{{ $a->check_in ?? '-' }}</td>
                        <td>{{ $a->check_out ?? '-' }}</td>
                        <td>
                            {{-- Applied status-badge class for consistent styling --}}
                            <span class="status-badge {{ strtolower($a->status) }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            No attendance records found for the selected employer.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- PAGINATION LINKS --}}
    {{-- Add this right after the table --}}
<div class="pagination-wrapper mt-3">
    {{ $attendances->links() }}
</div>
    </div>
</div>

@endsection