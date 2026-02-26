@extends('frontend.layouts.app')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .view-task-btn {
        background-color: #e9ecef;
        color: #212529 !important;
        padding: 5px 12px; /* reduced */
        border-radius: 6px;
        text-decoration: none !important;
        font-size: 12px; /* reduced */
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
        border: 1px solid #dee2e6;
        white-space: nowrap;
    }

    .view-task-btn:hover {
        background-color: #dee2e6;
        color: #000 !important;
    }

    .project-search {
        max-width: 280px;
    }

    /* 🔥 Compact Table Columns */
    #projectsTable th,
    #projectsTable td {
        padding: 0.6rem 0.75rem;
        vertical-align: middle;
        white-space: nowrap;
        text-align: left; 
    }

    #projectsTable th:nth-child(1),
    #projectsTable td:nth-child(1) {
        width: 60px;
        text-align: center; 
    }

    #projectsTable th:nth-child(4),
    #projectsTable td:nth-child(4) {
        width: 110px;
    }

    #projectsTable th:nth-child(5),
    #projectsTable td:nth-child(5) {
        width: 150px;
    }

    #projectsTable th:nth-child(6),
    #projectsTable td:nth-child(6) {
        width: 130px;
        text-align: left; 
    }
    .progress {
        height: 8px !important; 
        border-radius: 10px;
    }
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

<div class="container mt-4">

    <div class="page-header">
        <h2 class="fw-semibold mb-0">My Projects</h2>

        <input type="text"
               id="projectSearch"
               class="form-control project-search shadow-sm"
               placeholder="Search projects...">
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="projectsTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Project Name</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($projects as $key => $project)

                        @php $percent = $project->progress_percentage; @endphp

                        <tr>
                            <td>{{ $key + 1 }}</td>

                            <td class="fw-semibold text-dark">
                                {{ $project->name }}
                            </td>

                            <td class="text-muted">
                                {{ $project->client_name ?? '—' }}
                            </td>

                            <td>
                                <span class="badge 
                                    @if($project->status === 'ongoing') bg-primary 
                                    @elseif($project->status === 'completed') bg-success 
                                    @else bg-warning text-dark 
                                    @endif">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="grow">
                                        <div class="progress-bar 
                                            @if($percent < 40) bg-danger 
                                            @elseif($percent < 70) bg-warning 
                                            @else bg-success 
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $percent }}%;" 
                                            aria-valuenow="{{ $percent }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="ms-2 small fw-bold text-muted">
                                        {{ $percent }}%
                                    </span>
                                </div>
                            </td>

                            <td>
                                <a href="{{ route('employer.projects.tasks', $project->id) }}" class="view-task-btn">
                                    <i class="fas fa-list-check me-1"></i> View Tasks
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open d-block mb-2 fa-2x"></i>
                                No projects assigned yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($projects->hasPages())
    <div class="pagination-wrapper mt-3">
        {{ $projects->links() }}
    </div>
@endif
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.getElementById('projectSearch').addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#projectsTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
</script>
@endpush
