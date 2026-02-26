@extends('frontend.layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="users-header" style="margin-bottom: 20px;">
    <div class="header-left">
        <h2 style="font-size: 24px; font-weight: 600;">Leave Requests (Admin)</h2>
    </div>
</div>

<style>
    /* Pagination Styles */
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
    }

    .pagination li.active span {
        background: #000000 !important;
        color: #ffffff !important;
        border-color: #000000 !important;
    }

    /* Custom Toast Styles */
    .custom-toast {
        position: fixed;
        bottom: 30px;   /* Distance from bottom */
        right: 30px;    /* Distance from right */
        background: #fff;
        color: #111;
        padding: 14px 22px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 9999;
        opacity: 0;
        transform: translateY(20px); 
        transition: all 0.4s ease;    /* Animation speed */
    }

    .custom-toast.error {
        border-left: 5px solid #dc3545; /* Red indicator for error */
    }

    .custom-toast.show {
        opacity: 1;
        transform: translateY(0);    /* Slide up to original position */
    }
</style>

{{-- TABLE SECTION --}}
<div class="table-wrapper" style="background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">

    <table class="table custom-table users-table" style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <thead>
            <tr style="background:#f8f9fa;">
                <th style="width:24%; padding:12px 8px; text-align:center;">Employee</th>
                <th style="width:14%; padding:12px 8px; text-align:center;">From</th>
                <th style="width:14%; padding:12px 8px; text-align:center;">To</th>
                <th style="width:14%; padding:12px 8px; text-align:center;">Type</th>
                <th style="width:14%; padding:12px 8px; text-align:center;">Status</th>
                <th style="width:20%; padding:12px 8px; text-align:center;">Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($leaves as $leave)
                <tr id="leave-row-{{ $leave->id }}" style="border-bottom:1px solid #eee;">
                    <td style="padding:12px 8px; text-align:center;">{{ $leave->user->profile_display_name ?? 'N/A' }}</td>
                    <td style="padding:12px 8px; text-align:center;">{{ \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d') }}</td>
                    <td style="padding:12px 8px; text-align:center;">{{ \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d') }}</td>
                    <td style="padding:12px 8px; text-align:center;">{{ ucfirst($leave->type) }}</td>

                    <td style="padding:12px 8px; text-align:center;">
                        <span class="status-badge {{ $leave->status }}" style="padding:5px 12px; border-radius:18px; font-size:13px; font-weight:500;">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>

                    <td style="padding:12px 8px; text-align:center;">
                        @if($leave->status === 'pending')
                            <div style="display:flex; justify-content:center; gap:8px;">
                                <button onclick="approveLeave({{ $leave->id }})"
                                    style="padding:6px 14px; border:none; border-radius:6px; background:#e5e7eb; cursor:pointer; font-size:13px;">
                                    Approve
                                </button>

                                <button onclick="rejectLeave({{ $leave->id }})"
                                    style="padding:6px 14px; border:none; border-radius:6px; background:#e5e7eb; cursor:pointer; font-size:13px;">
                                    Reject
                                </button>
                            </div>
                        @else
                            <span style="color:#6c757d; font-size:13px;">Action Taken</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:20px; text-align:center;">No leave requests found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($leaves->hasPages())
        <div class="pagination-wrapper">
            {{ $leaves->links() }}
        </div>
    @endif
</div>

{{-- Toast Container --}}
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>

@endsection

@push('scripts')
<script>
/**
 * Handle Custom Toast Notification
 */
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    // Add base classes
    toast.className = 'custom-toast';
    if(type === 'error') toast.classList.add('error');
    
    toast.textContent = message;
    container.appendChild(toast);

    // Trigger animation after adding to DOM
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    // Auto remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

/**
 * AJAX Leave Approval
 */
function approveLeave(id) {
    if (!confirm('Approve this leave request?')) return;

    fetch(`/admin/leaves/${id}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, 'success');
        updateRowStatus(id, 'approved');
    })
    .catch((error) => {
        console.error('Error:', error);
        showToast('Something went wrong', 'error');
    });
}

/**
 * AJAX Leave Rejection
 */
function rejectLeave(id) {
    if (!confirm('Reject this leave request?')) return;

    fetch(`/admin/leaves/${id}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, 'success');
        updateRowStatus(id, 'rejected');
    })
    .catch((error) => {
        console.error('Error:', error);
        showToast('Something went wrong', 'error');
    });
}

/**
 * Update Table UI without Page Refresh
 */
function updateRowStatus(id, status) {
    const row = document.getElementById(`leave-row-${id}`);
    if (!row) return;

    const badge = row.querySelector('.status-badge');
    const actionCell = row.querySelector('td:last-child');

    if (badge) {
        badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        badge.className = `status-badge ${status}`;
    }

    if (actionCell) {
        actionCell.innerHTML = `
            <span style="color:#6c757d;font-size:13px;">
                Action Taken
            </span>
        `;
    }
}
</script>
@endpush