@extends('frontend.layouts.app')
@push('styles')
<style>
    /* Pagination wrapper settings */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 25px;
    }

    /* Hide the "Showing x to y" text */
    .pagination-wrapper nav div:first-child,
    .pagination-wrapper nav p {
        display: none !important;
    }

    /* Target the actual list */
    .pagination-wrapper .pagination {
        display: flex !important;
        list-style: none;
        padding: 0;
    }

    /* All page numbers/buttons */
    .pagination-wrapper .page-link {
        color: #000000 !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        padding: 6px 12px;
        min-width: 35px;
        text-align: center;
        text-decoration: none;
    }

    /* Active page (Black background) */
    .pagination-wrapper .page-item.active .page-link {
        background-color: #000000 !important;
        border-color: #000000 !important;
        color: #ffffff !important;
    }

    /* Hover effect */
    .pagination-wrapper .page-link:hover {
        background-color: #f3f4f6 !important;
        color: #000000 !important;
    }
    .toast {
    background: rgba(30, 30, 30, 0.95) !important;
    color: #ffffff !important;
    border-radius: 14px !important;
    padding: 8px 8px;
    min-width: 280px;
    backdrop-filter: blur(6px);
}
.toast-body {
    padding: 10px 14px !important;  
    line-height: 1.4;
}
/* ===== Project View Modal Custom Design ===== */

.project-view-modal .modal-body {
    padding: 30px !important;
}

.project-view-modal .project-view-buttons {
    margin-top: 35px !important;
}

.project-view-modal .modal-title {
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 20px;
}

.detail-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-box {
    background: #f8f9fa;
    border-radius: 14px;
    padding: 12px 15px;
    font-weight: 500;
    color: #212529;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.project-view-buttons .btn-close-custom {
    background-color: #7b1e24;
    color: #fff;
    border: none;
    border-radius: 10px;
}
.project-view-buttons .btn-edit-custom {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 10px;
}

.project-view-buttons .btn-edit-custom:hover {
    background-color: #0056b3;
}
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-5 w-100">
        <h2 class="fw-bold mb-0">All Projects</h2>

        <button type="button"
                class="btn btn-danger add-project-btn"
                data-bs-toggle="modal"
                data-bs-target="#addProjectModal">
            <i class="fas fa-plus me-1"></i> Add New Project
        </button>
    </div>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 project-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>Project Name</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th class="text-center" style="width:260px">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $project->name }}</td>
                            <td>{{ $project->client->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $statusColor = match($project->status) {
                                        'ongoing' => 'primary',
                                        'completed' => 'success',
                                        'on-hold' => 'warning',
                                        'pending' => 'secondary',
                                        default => 'info'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="action-container d-flex justify-content-center gap-2">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewProjectModal{{ $project->id }}">
                                         View
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editProjectModal{{ $project->id }}">
                                       Edit
                                    </button>

                                    <form action="{{ route('admin.projects.destroy', $project->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this project?');"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

{{-- VIEW DETAILS MODAL --}}
<div class="modal fade project-view-modal" id="viewProjectModal{{ $project->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-body">

                <h4 class="modal-title">Project Overview</h4>

                {{-- Project Name --}}
                <div class="mb-3">
                    <div class="detail-label">Project Name</div>
                    <div class="detail-box">
                        {{ $project->name }}
                    </div>
                </div>

                {{-- Client --}}
                <div class="mb-3">
                    <div class="detail-label">Client</div>
                    <div class="detail-box">
                        <i class="fas fa-building me-2 text-secondary"></i>
                        {{ $project->client->name ?? 'N/A' }}
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <div class="detail-label">Description</div>
                    <div class="detail-box" style="min-height:70px;">
                        {{ $project->description ?? 'No description provided.' }}
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <div class="detail-label">Status</div>
                    <div class="detail-box d-flex align-items-center">
                        <span class="status-dot bg-{{ $statusColor }}"></span>
                        {{ ucfirst($project->status) }}
                    </div>
                </div>

                {{-- Assigned Team --}}
                <div class="mb-5">
                    <div class="detail-label">Assigned Team</div>
                    <div class="detail-box">
                        @forelse($project->employers as $employer)
                            <div class="mb-1">
                                <i class="fas fa-user-circle me-2 text-secondary"></i>
                                {{ $employer->full_name }}
                            </div>
                        @empty
                            <span class="text-muted small">No members assigned</span>
                        @endforelse
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="project-view-buttons text-end">

                    <button type="button"
                            class="btn btn-close-custom px-4 py-2 fw-semibold"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
{{-- EDIT MODAL --}}
  <div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Edit Project Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                                    <form action="{{ route('admin.projects.update', $project->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Project Name</label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ $project->name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Client</label>
                                                    <select name="client_id" class="form-select rounded-3" required>
                                                        <option value="">Select Client</option>
                                                        @foreach(\App\Models\Client::all() as $client)
                                                            <option value="{{ $client->id }}" {{ $project->client_id == $client->id ? 'selected' : '' }}>
                                                                {{ $client->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Project Description</label>
                                                <textarea name="description" class="form-control rounded-3" rows="3">{{ $project->description }}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Status</label>
                                                    <select name="status" class="form-select rounded-3">
                                                        <option value="pending" {{ $project->status=='pending'?'selected':'' }}>Pending</option>
                                                        <option value="ongoing" {{ $project->status=='ongoing'?'selected':'' }}>Ongoing</option>
                                                        <option value="completed" {{ $project->status=='completed'?'selected':'' }}>Completed</option>
                                                        <option value="on-hold" {{ $project->status=='on-hold'?'selected':'' }}>On Hold</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Assign Employers</label>
                                                    <select name="employers[]" class="form-select rounded-3" multiple>
                                                        @foreach($employers as $employer)
                                                            <option value="{{ $employer->id }}"
                                                                {{ $project->employers->contains($employer->id) ? 'selected' : '' }}>
                                                                {{ $employer->full_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                    No projects found. Click "Add New Project" to start.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
{{-- ================= PAGINATION LINKS ================= --}}
@if($projects->hasPages())
    <div class="pagination-wrapper">
        {{ $projects->links('pagination::bootstrap-4') }}
    </div>
@endif
            </div>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-danger text-white" style="padding: 8px 20px;">
    <h5 class="modal-title fw-bold" style="line-height: 1;">Register New Project</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="margin: -0.5rem -0.5rem -0.5rem auto;"></button>
</div>
            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                
                <div class="modal-body px-4 pt-2 pb-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Project Name</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Enter project title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Client</label>
                            <select name="client_id" class="form-select rounded-3" required>
                                <option value="">Select Client</option>
                                @foreach(\App\Models\Client::all() as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Initial Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="pending">Pending</option>
                                <option value="ongoing" selected>Ongoing</option>
                                <option value="on-hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Assign Team</label>
                            <select name="employers[]" class="form-select rounded-3" multiple>
                                @foreach($employers as $employer)
                                    <option value="{{ $employer->id }}">{{ $employer->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1060;">
    <div id="successToast"
         class="toast align-items-center border-0 shadow-lg"
         role="alert"
         aria-live="assertive"
         aria-atomic="true">

        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="fas fa-check-circle text-success me-2"></i>
                {{ session('success') }}
            </div>

            <button type="button"
                    class="btn-close me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close">
            </button>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('successToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }
    });
</script>
@endsection
