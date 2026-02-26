@extends('frontend.layouts.app')

@section('content')
<style>
    /* ================= Layout Spacing ================= */
    .section-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-top: 0px; 
        margin-bottom: 8px; 
    }

    .section-title { 
        font-size: 1.1rem; 
        font-weight: 700; 
        color: #333;
        margin-top: 15px; 
        margin-bottom: 10px; 
    }

    .card { 
        border-radius: 12px; 
        border: none; 
        margin-top: 5px; 
    }
    
    .action-container {
        display: flex;
        gap: 8px; 
        justify-content: center;
        align-items: center;
        white-space: nowrap; 
    }

    .btn-custom-action {
        background-color: #e9ecef;
        color: #333;
        border: none;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
        line-height: 1.5;
    }
    .btn-custom-action:hover {
        background-color: #dee2e6;
        color: #000;
    }

    .btn-add-milestone {
        background-color: #000 !important;
        color: #fff !important;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 500;
        transition: 0.3s;
    }

    /* Status Dropdown */
    .status-dropdown {
        width: 130px;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 13px;
        border: 1px solid #ddd;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.2s;
        outline: none;
    }

    /* Project Header Card */
    .project-header-card {
        background: #fff;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 5px;
        height: 120px;      
        overflow: hidden;   
    }

    /* Pagination Styling */
    .pagination-wrapper nav div:first-child,
    .pagination-wrapper nav p {
        display: none !important;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
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

    /* ================= PROGRESS SLIDER CUSTOM COLOR ================= */
   
    .form-range {
        accent-color: #1a0000; 
    }
    .form-range::-webkit-slider-thumb {
        background-color: #1a0000 !important;
    }

    .form-range::-moz-range-thumb {
        background-color: #1a0000 !important;
    }

    .form-range:focus::-webkit-slider-thumb {
        box-shadow: 0 0 0 1px #fff, 0 0 0 0.25rem rgba(26, 0, 0, 0.25) !important;
    }
</style>

<div class="container mt-4">

    {{-- ================= PROJECT HEADER ================= --}}
    <div class="project-header-card">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 style="font-size: 26px; font-weight: 700; color: #333; margin-bottom: 8px;">
                    {{ $project->name }}
                </h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">Client: <strong>{{ $project->client_name ?? '—' }}</strong></span>
                    <span class="text-muted">|</span>
                    <span class="text-muted">Status: </span>
                    <span style="font-weight: 500; padding: 5px 12px;">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
            </div>
            <div style="text-align: right; min-width: 180px;">
                <div style="font-size: 24px; font-weight: 800; color: #a02020; line-height: 1;">
                    {{ $project->progress_percentage }}%
                </div>
                <div style="font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;">
                    Overall Progress
                </div>
            </div>
        </div>
    </div>

    {{-- ================= MILESTONES SECTION ================= --}}
    <div class="section-header">
        <div class="section-title m-0">Project Milestones</div>
        <button class="btn btn-add-milestone btn-sm shadow-sm" onclick="openMilestoneModal()">
            + Add Milestone
        </button>
    </div>

    <div class="card shadow-sm mb-4" style="overflow: hidden;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="table-layout: fixed; width: 100%; min-width: 800px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;" class="ps-4 py-3">Milestone Name</th>
                        <th style="width: 30%;">Progress Control</th>
                        <th style="width: 15%;" class="text-center">Status</th>
                        <th style="width: 30%;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                   @forelse($milestones as $milestone)
                    <tr>
                        <td class="ps-4 fw-medium text-dark" style="word-wrap: break-word; white-space: normal;">
                            {{ $milestone->title }}
                        </td>
                        <td>
                            <form id="update-form-{{ $milestone->id }}" action="{{ route('milestones.update_progress', $milestone->id) }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                <input type="range" name="progress" class="form-range me-2" min="0" max="100" step="5" value="{{ $milestone->progress }}" oninput="this.nextElementSibling.value = this.value + '%'">
                                <output class="fw-bold text-danger" style="min-width: 45px;">{{ $milestone->progress }}%</output>
                            </form>
                        </td>
                        <td class="text-center">
                            <span style="font-size: 14px;">
                                {{ $milestone->progress == 100 ? 'Completed' : 'In Progress' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-container">
                                <button type="submit" form="update-form-{{ $milestone->id }}" class="btn-custom-action">Update</button>
                                <button type="button" class="btn-custom-action" onclick="openMilestoneModal('{{ $milestone->id }}', '{{ $milestone->title }}', '{{ $milestone->progress }}')">Edit</button>
                                <form action="{{ route('milestones.destroy', $milestone->id) }}" method="POST" onsubmit="return confirm('Delete this milestone?')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-custom-action">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No milestones added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($milestones->hasPages())
                <div class="pagination-wrapper">
                    {{ $milestones->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ================= TASKS SECTION ================= --}}
    <div class="section-title">Project Tasks</div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Task Title</th>
                        <th>Assigned To</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $key => $task)
                    <tr>
                        <td class="ps-4">
                            {{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}
                        </td>
                        <td class="fw-medium">{{ $task->task }}</td>
                        <td>{{ $task->user->name ?? 'Employer' }}</td>
                        <td class="text-center">
                            <span class="text-dark" style="font-size: 13px; font-weight: 600;">
                                {{ strtoupper($task->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('tasks.update', $task->id) }}">
                                @csrf @method('PUT')
                                <select name="status" class="status-dropdown mx-auto" onchange="this.form.submit()">
                                    <option value="Pending" {{ $task->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Completed" {{ $task->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($tasks->hasPages())
                <div class="pagination-wrapper">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal section --}}
<div class="modal fade" id="milestoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="milestoneForm" action="{{ route('milestones.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="milestoneModalLabel">Add Milestone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Milestone Name</label>
                    <input type="text" name="title" id="milestoneTitle" class="form-control" placeholder="Enter milestone name" required style="border-radius: 8px; padding: 10px;">
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted">Progress (%)</label>
                    <input type="number" name="progress" id="milestoneProgress" class="form-control" value="0" min="0" max="100" style="border-radius: 8px; padding: 10px;">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <button type="submit" class="btn btn-dark px-4" id="saveButton" style="border-radius: 8px;">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMilestoneModal(id = null, title = '', progress = 0) {
        const form = document.getElementById('milestoneForm');
        const titleInput = document.getElementById('milestoneTitle');
        const progressInput = document.getElementById('milestoneProgress');
        const modalLabel = document.getElementById('milestoneModalLabel');
        const methodField = document.getElementById('methodField');
        const saveButton = document.getElementById('saveButton');

        if (id) {
            modalLabel.innerText = 'Edit Milestone';
            saveButton.innerText = 'Update Milestone';
            form.action = "{{ url('milestones') }}/" + id + "/update-progress"; 
            methodField.innerHTML = ''; 
            titleInput.value = title;
            progressInput.value = progress;
        } else {
            modalLabel.innerText = 'Add Milestone';
            saveButton.innerText = 'Save Milestone';
            form.action = "{{ route('milestones.store') }}";
            methodField.innerHTML = '';
            titleInput.value = '';
            progressInput.value = 0;
        }

        var myModal = new bootstrap.Modal(document.getElementById('milestoneModal'));
        myModal.show();
    }
</script>
@endsection