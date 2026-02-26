@extends('frontend.layouts.app')

{{-- Push custom CSS for table, modals, and buttons --}}
@push('styles')
<style>
    /* Page body */
    body { background:#f8f9fc; font-family:'Inter',sans-serif; }

    /* Table wrapper styling */
    .table-wrapper { 
        background:#fff; 
        padding:22px; 
        border-radius:22px; 
        box-shadow:0 25px 60px rgba(0,0,0,.12); 
    }

    /* Center align text in table */
    .modern-table th.text-center, .modern-table td.text-center { 
        text-align: center !important; 
        vertical-align: middle; 
    }

    /* Action column layout */
    .action-col { 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        gap: 8px; 
        width: 100%; 
    }
    .action-col form { margin: 0; display: flex; }

    /* Buttons in action column */
    .btn-action-custom { 
        padding: 6px 14px; 
        background: #f1f5f9; 
        border: 1px solid #e2e8f0; 
        border-radius: 8px; 
        font-size: 13px; 
        cursor: pointer; 
        transition: 0.2s ease; 
        text-decoration: none; 
        color: #1e293b; 
    }
    .btn-action-custom:hover { background: #e2e8f0; }

    /* Modal overlay */
    .centered-overlay { 
        position:fixed; inset:0; 
        background:rgba(15,23,42,.6); 
        display:none; 
        justify-content:center; 
        align-items:center; 
        z-index:9999; 
    }

    /* Modal card for time logs */
    .timelog-modal-card { 
        position:relative; 
        background:#fff; 
        animation:fadeIn .2s ease; 
    }

    /* Modal header */
    .modal-header-custom { 
        display:flex; 
        justify-content:space-between; 
        align-items:center; 
    }
    .modal-title-custom { font-weight:700; font-size:18px; color:#1e293b; }
    .btn-close-circle { 
        width:34px; height:34px; border-radius:50%; 
        border:none; background:#f1f5f9; cursor:pointer; 
        display:flex; align-items:center; justify-content:center; 
    }

    /* Detail rows in modal */
    .detail-item-dashed { 
        display:flex; justify-content:space-between; padding:12px 0; 
        border-bottom:1px dashed #e2e8f0; 
    }
    .detail-label { color:#64748b; font-size:14px; }
    .detail-value { font-weight:600; font-size:14px; }

    /* Save button styling */
    .btn-save-custom {
        background: #7c1616 !important;
        color: #ffffff !important;
        width: auto !important;
        padding: 10px 26px !important;
        border-radius: 10px !important;
        border: none !important;
        font-weight: 100 !important;
        cursor: pointer;
        transition: 0.2s;
        display: block !important;
        margin-left: auto !important; /* Right aligned */
    }

    @keyframes fadeIn { 
        from { opacity:0; transform:scale(.95); } 
        to { opacity:1; transform:scale(1); } 
    }

    /* Pagination styling */
    .pagination-wrapper { display: flex; justify-content: center; margin-top: 25px; margin-bottom: 20px; }
    .pagination-wrapper nav div:first-child, .pagination-wrapper nav p { display: none !important; }
    .pagination-wrapper nav svg { width: 20px; }
    .pagination-wrapper ul.pagination, .pagination-wrapper nav div:last-child ul { display: flex !important; list-style: none; padding: 0; margin: 0; }
    .pagination-wrapper li a, .pagination-wrapper li span { 
        min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; 
        background-color: #ffffff !important; color: #000000 !important; border: 1px solid #d1d5db !important; 
        text-decoration: none; font-size: 13px; font-weight: 500; margin-left: -1px; transition: all 0.2s; 
    }
    .pagination-wrapper li.active span { 
        background: #000000 !important; color: #ffffff !important; border-color: #000000 !important; z-index: 10; 
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 position-relative">

    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">
            All Employee Work Logs
        </h4>
    </div>

    {{-- Table wrapper --}}
    <div class="table-wrapper card border-0 shadow-sm" style="border-radius: 22px;">
        <div class="card-body p-0">
            <div class="table-responsive" style="border-radius: 22px;">
                <table class="modern-table table mb-0 align-middle" style="table-layout: fixed; width: 100%; min-width: 1050px;">
                    <thead>
                        <tr>
                            <th style="width:100px" class="px-3 py-3 text-secondary fw-semibold">Date</th>
                            <th style="width:130px" class="px-3 py-3 text-secondary fw-semibold">Employee</th>
                            <th style="width:150px" class="px-3 py-3 text-secondary fw-semibold">Project</th>
                            <th style="width:150px" class="px-3 py-3 text-secondary fw-semibold">Task</th>
                            <th style="width:200px" class="px-3 py-3 text-secondary fw-semibold">Description</th>
                            <th style="width:80px" class="px-3 py-3 text-secondary fw-semibold text-center">Logged</th>
                            <th class="px-3 py-3 text-secondary fw-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop through each work log --}}
                        @forelse($timeLogs as $log)
                        <tr style="border-bottom:1px solid #f2f4f7;">
                            <td class="px-3 text-dark" style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($log->log_date)->format('Y-m-d') }}
                            </td>
                            <td class="px-3 text-dark fw-bold text-truncate" style="font-size:13px;">
                                {{ $log->user->full_name ?? 'N/A' }}
                            </td>
                            <td class="px-3 text-dark text-truncate" style="font-size:13px;">
                                {{ $log->task->project->name ?? 'No Project' }}
                            </td>
                            <td class="px-3 text-muted text-truncate" style="font-size:13px;">
                                {{ $log->task->title ?? 'General' }}
                            </td>
                            <td class="px-3 text-muted text-truncate" style="font-size:13px;" title="{{ $log->description }}">
                                {{ $log->description ?: '-' }}
                            </td>
                            <td class="px-3 text-center">
                                <span class="badge bg-light text-dark px-2 py-2" style="font-size:12px; border-radius:6px;">
                                    {{ number_format($log->hours, 2) }}h
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                {{-- Action buttons: Show, Edit, Delete --}}
                                <div class="action-col">
                                    <button type="button" class="btn-action-custom" onclick="toggleOverlay('log-overlay-{{ $log->id }}')">Show</button>
                                    <button type="button" class="btn-action-custom" onclick="toggleOverlay('edit-overlay-{{ $log->id }}')">Edit</button>
                                    <form action="{{ route('time-logs.destroy', $log->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-custom" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No work logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="pagination-wrapper">
                    {{ $timeLogs->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Loop for modal overlays per work log --}}
    @foreach($timeLogs as $log)
        {{-- Edit Modal --}}
        <div id="edit-overlay-{{ $log->id }}" class="centered-overlay">
            <div class="timelog-modal-card" style="width: 420px; border-radius: 20px; overflow: hidden; padding: 0;">
                <div style="padding: 25px; position: relative;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #2d3748;">Edit Work Log</h3>
                    <button type="button" onclick="toggleOverlay('edit-overlay-{{ $log->id }}')" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 18px; cursor: pointer; color: #4a5568;">✕</button>
                </div>

                <form action="{{ route('time-logs.update', $log->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="task_id" value="{{ $log->task_id }}">

                    {{-- Form inputs: Hours, Minutes, Date, Description --}}
                    <div style="padding: 0 25px 20px 25px;">
                        <div style="padding: 12px 18px; margin-bottom: 20px; background-color: #f0f7ff; border-radius: 12px; border: 1px solid #e1efff;">
                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #2d3748;">
                                {{ $log->task->project->name ?? 'General' }} | {{ $log->task->title ?? 'General' }}
                            </p>
                        </div>

                        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Hours</label>
                                <select name="hours" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;" required>
                                    @for ($i = 0; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ floor($log->hours) == $i ? 'selected' : '' }}>{{ $i }} hr</option>
                                    @endfor
                                </select>
                            </div>

                            <div style="flex: 1;">
                                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Minutes</label>
                                <select name="minutes" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;" required>
                                    @php $currentMins = round(($log->hours - floor($log->hours)) * 60); @endphp
                                    @foreach([0,15,30,45] as $m)
                                        <option value="{{ $m }}" {{ $currentMins == $m ? 'selected' : '' }}>{{ sprintf('%02d',$m) }} min</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Log Date --}}
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Date</label>
                            <input type="date" name="log_date" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;" value="{{ \Carbon\Carbon::parse($log->log_date)->format('Y-m-d') }}" required>
                        </div>

                        {{-- Description --}}
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Description</label>
                            <textarea name="description" rows="3" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; resize: none;">{{ $log->description }}</textarea>
                        </div>
                    </div>

                    {{-- Save button aligned right --}}
                    <div style="padding: 0 25px 25px 25px;">
                        <button type="submit" class="btn-save-custom">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Show Work Log Details Modal --}}
        <div id="log-overlay-{{ $log->id }}" class="centered-overlay">
            <div class="timelog-modal-card" style="width: 420px; padding: 30px; border-radius: 20px;">
                <div class="modal-header-custom mb-3">
                    <h4 class="modal-title-custom">Work Log Details</h4>
                    <button type="button" class="btn-close-circle" onclick="toggleOverlay('log-overlay-{{ $log->id }}')">✕</button>
                </div>
                <div class="detail-item-dashed"><span class="detail-label">Employee</span><span class="detail-value">{{ $log->user->full_name ?? 'N/A' }}</span></div>
                <div class="detail-item-dashed"><span class="detail-label">Project</span><span class="detail-value">{{ $log->task->project->name ?? 'N/A' }}</span></div>
                <div class="detail-item-dashed"><span class="detail-label">Task</span><span class="detail-value">{{ $log->task->title ?? 'General' }}</span></div>
                <div class="detail-item-dashed"><span class="detail-label">Log Date</span><span class="detail-value">{{ \Carbon\Carbon::parse($log->log_date)->format('Y-m-d (l)') }}</span></div>
                <div class="detail-item-dashed border-0"><span class="detail-label">Description</span><span class="detail-value text-muted">{{ $log->description ?: '-' }}</span></div>
                <div style="margin-top: 25px;">
                    <button type="button" style="width: 100%; padding: 12px; border-radius: 10px; border: none; background: #f1f1f1; font-weight: 600;" onclick="toggleOverlay('log-overlay-{{ $log->id }}')">Close</button>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

{{-- Scripts --}}
@push('scripts')
<script>
    // Toggle overlay visibility
    function toggleOverlay(id){
        const el = document.getElementById(id);
        if(!el) return;
        const isOpen = el.style.display === "flex";
        el.style.display = isOpen ? "none" : "flex";
        document.body.style.overflow = isOpen ? "auto" : "hidden";
    }

    // Show toast notification if success message exists
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showToast === "function") {
                showToast("{{ session('success') }}");
            }
        });
    @endif
</script>
@endpush