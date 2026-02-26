@extends('frontend.layouts.app')

@section('content')

{{-- ================= HEADER & FILTER SECTION (Outside the card) ================= --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    {{-- Page Title --}}
    <h2 style="margin:0; font-size: 24px; font-weight: 600;">All Employers Daily Tasks</h2>

   {{-- Employer Filter Dropdown --}}
<form method="GET" action="{{ route('admin.tasks.index') }}" class="task-filter-form" style="display: inline-block; margin-right: 10px;">
    <label for="employer_id" style="margin-right: 5px; ">Filter by Employee:</label>
    <select name="employer_id" id="employer_id"
            onchange="this.form.submit()" 
            class="form-select task-filter-select" 
            style="width: 220px; border-radius: 6px; border: 1px solid #ccc; padding: 6px;">
        <option value="">-- Select Employee --</option>
        @foreach($employers as $employer)
            <option value="{{ $employer->id }}" 
                {{ request('employer_id') == $employer->id ? 'selected' : '' }}>
                {{ $employer->full_name }}
            </option>
        @endforeach
    </select>
</form>
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

    {{-- ================= TASKS TABLE ================= --}}
    <div class="table-wrapper">
     <table class="table custom-table users-table admin-task-table">
            <thead>
                <tr>
                    <th>Employer</th>
                    <th>Task</th>
                    <th>Sub Task</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>
                            {{ $task->user?->profile_display_name ?? 'N/A' }}
                        </td>
                        <td>{{ $task->task }}</td>
                        <td>{{ $task->sub_task }}</td>
                        <td>{{ $task->start_date }}</td>
                        <td>{{ $task->end_date }}</td>
                        <td>
                            {{-- Optional: You can add a span with class for color coding status --}}
                            <span class="status-badge {{ strtolower($task->status) }}">
                                {{ strtoupper($task->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            No tasks found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($tasks, 'links'))
            <div class="pagination-wrapper">
                {{ $tasks->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection