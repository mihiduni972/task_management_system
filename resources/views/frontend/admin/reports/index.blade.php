@extends('frontend.layouts.app')
<style>/* Filter Bar Container */
.filter-bar {
    display: flex;
    gap: 12px;
    align-items: center;
    margin: 15px 0 25px 0;
    flex-wrap: wrap;
}

/* Dropdown (All Projects) */
.filter-bar select {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    background-color: #fff;
    font-size: 14px;
    min-width: 180px;
    transition: 0.2s ease-in-out;
    cursor: pointer;
}

/* Date Inputs */
.filter-bar input[type="date"] {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.2s ease-in-out;
}

/* Focus Effect */
.filter-bar select:focus,
.filter-bar input[type="date"]:focus {
    border-color: #8b0000;
    box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.2);
    outline: none;
}

/* Apply Button */
.filter-bar .btn-export-csv {
    padding: 8px 18px;
    border-radius: 6px;
    background-color: #8b0000;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: 0.2s;
}
.filter-bar .btn-refresh {
    padding: 8px 18px;
    border-radius: 6px;
    background-color: #f5f6f8;
    color: #333;
    border: 1px solid #e2e2e2;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
</style>
@section('content')
<div class="page-header">
    <h2 class="page-title">Dashboard</h2>

    @auth
        @if(auth()->user()->user_type == 'admin')
        <div class="export-buttons">
          <a href="{{ route('admin.export.employees') }}" class="btn-export-csv">
    ⬇ Export Employees CSV
              </a>
          <a href="{{ route('admin.export.tasks') }}" class="btn-export-csv">
    ⬇ Export Tasks CSV
             </a>
             <a href="{{ route('admin.export.projects') }}" class="btn-export-csv">
      ⬇ Export Projects CSV
           </a>
           <a href="{{ route('admin.backup.database') }}" class="btn-export-csv">
    ⬇ Download System Backup
           </a>
        </div>
        @endif
    @endauth
</div>

    {{-- MAIN STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <span>Total Employees</span>
            <strong>{{ $totalEmployees }}</strong>
        </div>
        <div class="stat-card">
            <span>Total Tasks</span>
            <strong>{{ $totalTasks }}</strong>
        </div>
        <div class="stat-card highlight">
            <span>Task Completion Rate</span>
            <strong>{{ $taskCompletionRate }}%</strong>
        </div>
        <div class="stat-card">
            <span>Total Projects</span>
            <strong>{{ $totalProjects }}</strong>
        </div>
    </div>
<form method="GET" class="filter-bar">
    <select name="project_id">
        <option value="">All Projects</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}"
                {{ $selectedProject == $project->id ? 'selected' : '' }}>
                {{ $project->name }}
            </option>
        @endforeach
    </select>

    <input type="date" name="from" value="{{ $from }}">
    <input type="date" name="to" value="{{ $to }}">

    <button type="submit" class="btn-export-csv">Apply</button>

    <a href="{{ route('admin.reports.index') }}" class="btn-refresh">
    ⟳ Refresh
</a>
</form>
    {{-- CHARTS AREA --}}
    <div class="charts-grid">
        {{-- 1. Employee Performance (Bar Chart) --}}
        <div class="chart-card">
            <h4>Employee vs Completed Tasks</h4>
            <div style="flex:1;">
                <canvas id="employeePerformanceChart"></canvas>
            </div>
        </div>

        {{-- 2. Task Completion Rate (Pie Chart) --}}
        <div class="chart-card">
            <h4>Task Completion Rate</h4>
            <div style="flex:1;">
                <canvas id="taskCompletionPieChart"></canvas>
            </div>
        </div>

        {{-- 3. Project Summary (Progress Bars List) --}}
        <div class="chart-card">
            <h4>Project Task Progress</h4>
            <div class="project-list-container">
                @foreach($projectSummary as $project)
                    @php
                        $percentage = $project->tasks_count > 0 
                            ? round(($project->completed_tasks_count / $project->tasks_count) * 100) 
                            : 0;
                    @endphp
                    <div class="project-item">
                        <div class="project-info">
                            <strong>{{ $project->name }}</strong>
                            <span>{{ $percentage }}% ({{ $project->completed_tasks_count }}/{{ $project->tasks_count }})</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $percentage }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.size = 11;

    // 1. Employee Performance (Bar Chart)
    new Chart(document.getElementById('employeePerformanceChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($employeePerformance->pluck('username')) !!},
            datasets: [{
                label: 'Completed Tasks',
                data: {!! json_encode($employeePerformance->pluck('completed_task_count')) !!},
                backgroundColor: '#8b0000',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 2. Task Completion Rate (Pie Chart)
    new Chart(document.getElementById('taskCompletionPieChart'), {
        type: 'pie',
        data: {
            labels: ['Completed', 'Pending'],
            datasets: [{
                data: [{{ $completedTasks }}, {{ $pendingTasks }}],
                backgroundColor: ['#4b0000', '#e5e7eb'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush
