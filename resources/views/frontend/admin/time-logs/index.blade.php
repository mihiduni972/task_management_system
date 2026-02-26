@extends('frontend.layouts.app')

@push('styles')
    {{-- Link tasks.css file for table and modal styling --}}
    <link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="work-log-card">
        <h4 class="card-title">
            <i class="fas fa-clock mr-2"></i> All Employee Work Logs
        </h4>

        {{-- ================= FILTER SECTION ================= --}}
        <div class="filter-section">
            <form action="{{ route('admin.time-logs.index') }}" method="GET" class="filter-group">
                {{-- Select Employee --}}
                <div>
                    <label>Select Employee</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- All Employees --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Date --}}
                <div>
                    <label>Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>

                {{-- Filter & Reset Buttons --}}
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.time-logs.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        {{-- ================= PAGINATION STYLING ================= --}}
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

        {{-- ================= WORK LOG TABLE ================= --}}
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee (ID & Name)</th>
                        <th>Project & Task</th>
                        <th>Logged Hours</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timeLogs as $log)
                        <tr>
                            {{-- Log date --}}
                            <td>{{ $log->created_at->format('Y-m-d') }}</td>

                            {{-- Employee ID and Name --}}
                            <td>
                                <strong>ID:</strong> {{ $log->user_id }} <br>
                                <strong>Name:</strong> {{ optional($log->user)->full_name ?? 'User Not Linked' }}
                            </td>

                            {{-- Project and Task --}}
                            <td>
                                <div>{{ $log->task->project->name ?? 'No Project' }}</div>
                                <small class="text-muted">{{ $log->task->title ?? 'No Task' }}</small>
                            </td>

                            {{-- Logged Hours --}}
                            <td><span class="hours-badge">{{ number_format($log->hours, 2) }} Hours</span></td>

                            {{-- Description (limited to 50 chars) --}}
                            <td>{{ Str::limit($log->description, 50) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= PAGINATION ================= --}}
        @if($timeLogs->hasPages())
            <div class="pagination-wrapper">
                {{ $timeLogs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection