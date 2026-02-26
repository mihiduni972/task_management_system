@extends('frontend.layouts.app')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<div class="page-header">
    <h2 class="page-title">My Leaves</h2>

    <button class="btn btn-danger add-btn" onclick="openLeaveModal()">
    +Add Leave
</button>
</div>

<style>
    .add-btn {
        border-radius: 8px !important; 
        padding: 10px 20px; 
        font-weight: 600;
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

{{-- ================= REFRESHABLE CONTAINER ================= --}}
<div class="table-wrapper" id="leave-refresh-container">
    <table class="table custom-table leave-table" id="leaveTable">
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Type</th>
                <th>Status</th>
                <th class="action-col">Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($leaves as $l)
            <tr id="leave-row-{{ $l->id }}">
                <td>{{ \Carbon\Carbon::parse($l->from_date)->format('Y-m-d') }}</td>
                <td>{{ \Carbon\Carbon::parse($l->to_date)->format('Y-m-d') }}</td>
                <td>{{ ucfirst($l->type) }}</td>

                <td>
                    <span class="status-badge {{ $l->status }}">
                        {{ ucfirst($l->status) }}
                    </span>
                </td>

                <td class="action-col">
                    <div class="action-container">
                        <button class="btn-action" onclick="viewLeave({{ $l->id }})">Show</button>
                        <button class="btn-action" onclick="openEditLeaveModal({{ $l->id }})">Edit</button>
                        <button class="btn-action" onclick="deleteLeave({{ $l->id }})">Delete</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4">
                    No leave records found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINATION LINKS --}}
    <div class="pagination-wrapper mt-3">
        {{ $leaves->links() }}
    </div>
</div>

@include('frontend.leaves.modal')

@endsection

@push('scripts')
<script>
/**
 * Refreshes the table container after an AJAX action (Add/Edit/Delete)
 * It fetches the current page and extracts the table container to avoid full refresh.
 */
function refreshTable() {
    fetch(window.location.href) 
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableContent = doc.getElementById('leave-refresh-container').innerHTML;
            
            // Replace the current container content with the new one
            document.getElementById('leave-refresh-container').innerHTML = newTableContent;
        })
        .catch(error => console.error('Error refreshing table:', error));
}

/**
 * Fetch and display leave details in the view modal
 */
function viewLeave(id) {
    fetch(`/employer/leaves/${id}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            // Populate modal placeholders
            if(document.getElementById('view_type')) document.getElementById('view_type').innerText = data.type;
            if(document.getElementById('view_from')) document.getElementById('view_from').innerText = data.from_date;
            if(document.getElementById('view_to')) document.getElementById('view_to').innerText = data.to_date;
            if(document.getElementById('view_status')) document.getElementById('view_status').innerText = data.status;
            if(document.getElementById('view_reason')) document.getElementById('view_reason').innerText = data.reason || 'No reason provided';

            // Show the modal
            const viewModal = document.getElementById('viewLeaveModal');
            if (viewModal) viewModal.style.display = 'flex';
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Something went wrong while fetching leave details.');
        });
}

/**
 * Close the view leave modal
 */
function closeViewLeaveModal() {
    const viewModal = document.getElementById('viewLeaveModal');
    if (viewModal) viewModal.style.display = 'none';
}

// NOTE: Remember to call refreshTable() inside your AJAX success blocks 
// for store, update, and delete functions!
</script>
@endpush