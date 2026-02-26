@extends('frontend.layouts.app')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<div class="users-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div class="header-left">
        <h2 style="font-size: 24px; font-weight: 600; margin: 0;">My Attendance</h2>
    </div>
    <div class="header-right">
        <button class="add-btn" id="employerAddAttendanceBtn" style="border-radius: 8px; padding: 10px 20px;">
            + Add Attendance
        </button>
    </div>
</div>

<style>
    /* Pagination CSS */
    .pagination-wrapper nav div:first-child, .pagination-wrapper nav p { display: none !important; }
    .pagination-wrapper { display: flex; justify-content: center; margin-top: 25px; }
    .pagination { display: flex !important; list-style: none; padding: 0; margin: 0; }
    .pagination li a, .pagination li span {
        min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
        background-color: #ffffff !important; color: #000000 !important; border: 1px solid #d1d5db !important;
        text-decoration: none; font-size: 13px; font-weight: 500; margin-left: -1px;
    }
    .pagination li.active span { background: #000000 !important; color: #ffffff !important; border-color: #000000 !important; }

    /* IMPROVED TOAST STYLE */

#toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #ffffff; 
    color: #111111; 
    padding: 12px 24px;
    border-radius: 8px;
    z-index: 999999;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); 
    border: 1px solid #e0e0e0;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease-in-out;
}

#toast.show {
    opacity: 1;
    transform: translateY(0);
}
</style>

<div class="table-wrapper" id="attendance-refresh-container">
    <table class="table users-table" id="attendanceTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Total Hours</th>
                <th class="action-col">Action</th>
            </tr>
        </thead>
        <tbody id="attendanceTableBody">
        @forelse($attendances as $row)
            <tr id="row-{{ $row->id }}">
                <td>{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}</td>
                <td>
                    <span class="status-badge {{ strtolower($row->status) }}">
                        {{ ucfirst($row->status) }}
                    </span>
                </td>
                <td>{{ $row->check_in ?? '-' }}</td>
                <td>{{ $row->check_out ?? '-' }}</td>
                <td>{{ $row->worked_minutes ? number_format($row->worked_minutes / 60, 2) . ' hrs' : '-' }}</td>
                <td class="action-col">
                    <div class="action-container">
                        <button class="table-btn showBtn" data-id="{{ $row->id }}">Show</button>
                        <button class="table-btn editBtn" data-id="{{ $row->id }}">Edit</button>
                        <button class="table-btn deleteBtn" data-id="{{ $row->id }}">Delete</button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No records found</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pagination-wrapper mt-3">{{ $attendances->links() }}</div>
</div>

{{-- Details Overlay --}}
<div class="details-overlay" id="attendanceDetailsOverlay" style="display:none;">
    <div class="details-card-modern">
        <button type="button" class="close-x-btn" id="closeDetailsBtnX">✕</button>
        <div class="details-header-modern"><h3>Attendance Details</h3></div>
        <div class="details-body-modern">
            <div class="detail-item-modern"><span class="label">Date</span><span class="value" id="detailDate">-</span></div>
            <div class="detail-item-modern"><span class="label">Status</span><span class="value" id="detailStatusBadge"><span id="detailStatus" class="status-badge">-</span></span></div>
            <div class="detail-item-modern"><span class="label">Check In</span><span class="value" id="detailCheckIn">-</span></div>
            <div class="detail-item-modern"><span class="label">Check Out</span><span class="value" id="detailCheckOut">-</span></div>
            <div class="detail-item-modern no-border"><span class="label">Worked Hours</span><span class="value" id="detailHours">-</span></div>
        </div>
        <div class="details-footer-modern"><button class="footer-close-btn" id="closeDetailsBtn">Close</button></div>
    </div>
</div>

@include('frontend.attendance.employer.modal')

{{-- Toast Container --}}
<div id="toast"></div>

@endsection

@push('scripts')
<script>
/* TOAST FUNCTION WITH ANIMATION */
function showToast(msg) {
    console.log("Showing Toast: " + msg);
    const toast = document.getElementById('toast');
    if(!toast) return;

    toast.innerText = msg;
    toast.classList.add('show');

    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

/* TABLE REFRESH */
function refreshTable() {
    const url = window.location.href;
    fetch(url)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('attendance-refresh-container').innerHTML;
            document.getElementById('attendance-refresh-container').innerHTML = newContent;
            console.log("Table refreshed successfully.");
        })
        .catch(err => console.error("Refresh Error:", err));
}

document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.getElementById('employerAddAttendanceBtn');
    const modal  = document.getElementById('employerAddAttendanceModal');
    const form   = document.getElementById('employerAttendanceForm');
    let editId   = null;

    addBtn?.addEventListener('click', () => {
        editId = null;
        form.reset();
        if(modal.querySelector('.modal-title')) modal.querySelector('.modal-title').innerText = 'Add Attendance';
        modal.classList.add('active');
    });

    modal?.addEventListener('click', (e) => {
        if (e.target.id === 'employerAddAttendanceModal') {
            modal.classList.remove('active');
            form.reset();
        }
    });

   
    document.addEventListener('click', async (e) => {
        // Show
        if (e.target.classList.contains('showBtn')) {
            const id = e.target.dataset.id;
            const res = await fetch(`/my-attendance/${id}`);
            const data = await res.json();
            document.getElementById('detailDate').innerText = data.date;
            document.getElementById('detailStatus').innerText = data.status;
            document.getElementById('detailCheckIn').innerText = data.check_in;
            document.getElementById('detailCheckOut').innerText = data.check_out;
            document.getElementById('detailHours').innerText = data.hours;
            document.getElementById('attendanceDetailsOverlay').style.display = 'flex';
        }

        // Edit
        if (e.target.classList.contains('editBtn')) {
            editId = e.target.dataset.id;
            const res = await fetch(`/my-attendance/${editId}`);
            const data = await res.json();
            form.querySelector('[name="date"]').value = data.date;
            form.querySelector('[name="status"]').value = data.status.toLowerCase();
            form.querySelector('[name="check_in"]').value = data.check_in === '-' ? '' : data.check_in;
            form.querySelector('[name="check_out"]').value = data.check_out === '-' ? '' : data.check_out;
            if(modal.querySelector('.modal-title')) modal.querySelector('.modal-title').innerText = 'Edit Attendance';
            modal.classList.add('active');
        }

        // Delete
        if (e.target.classList.contains('deleteBtn')) {
            const id = e.target.dataset.id;
            if (!confirm('Delete this record?')) return;
            const res = await fetch(`/my-attendance/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message || 'Attendance deleted successfully');
                refreshTable();
            }
        }
    });

    // Form Submit (Add/Update)
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        if (editId) formData.append('attendance_id', editId);

        try {
            const res = await fetch(`{{ route('attendance.employer.store') }}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const data = await res.json();
            console.log("Server Response:", data);

            if (data.success) {
                modal.classList.remove('active');
                
                
                const successMsg = editId ? 'Attendance updated successfully' : 'Attendance added successfully';
                showToast(data.message || successMsg);
                
                refreshTable();
                form.reset();
                editId = null;
            } else {
                showToast('Something went wrong!');
            }
        } catch (error) {
            console.error("Submission Error:", error);
            showToast('Error connecting to server');
        }
    });

    // Close Details Buttons
    document.getElementById('closeDetailsBtn')?.addEventListener('click', () => { 
        document.getElementById('attendanceDetailsOverlay').style.display = 'none'; 
    });
    document.getElementById('closeDetailsBtnX')?.addEventListener('click', () => { 
        document.getElementById('attendanceDetailsOverlay').style.display = 'none'; 
    });
});
</script>
@endpush