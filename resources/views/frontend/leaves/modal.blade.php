{{-- ================= LEAVE MODAL (ADD/EDIT) ================= --}}
<div class="modal-overlay" id="leaveModal" style="display:none;">
    <div class="modal-card">
        <button class="close-modal" onclick="closeLeaveModal()">&times;</button>
        <h2 class="modal-title" id="leaveModalTitle">Add New Leave</h2>

        <form id="leaveForm">
            @csrf
            <input type="hidden" id="leave_id">

            <div class="form-group">
                <label>Leave Type</label>
                <select id="type" required>
                    <option value="">-- Select Type --</option>
                    <option value="casual">Casual</option>
                    <option value="sick">Sick</option>
                    <option value="annual">Annual</option>
                </select>
            </div>

            <div class="form-group">
                <label>Reason</label>
                <textarea id="reason" rows="2" placeholder="Enter reason (optional)"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group half-width">
                    <label>From Date</label>
                    <input type="date" id="from_date" required>
                </div>
                <div class="form-group half-width">
                    <label>To Date</label>
                    <input type="date" id="to_date" required>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit">Submit</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= VIEW LEAVE MODAL ================= --}}
<div class="modal-overlay" id="viewLeaveModal" style="display:none;">
    <div class="modal-card">
        <button class="close-modal" onclick="closeViewLeaveModal()">&times;</button>
        <h2 class="modal-title">Leave Details</h2>
        
        <div style="padding: 20px; line-height: 2;">
            <p><strong>Type:</strong> <span id="view_type"></span></p>
            <p><strong>From:</strong> <span id="view_from"></span></p>
            <p><strong>To:</strong> <span id="view_to"></span></p>
            <p><strong>Status:</strong> <span id="view_status"></span></p>
            <p><strong>Reason:</strong></p>
            <div id="view_reason" style="background: #f9f9f9; padding: 10px; border-radius: 8px; border: 1px solid #eee; line-height: 1.5;"></div>
        </div>

        <div class="form-footer" style="display: flex; justify-content: flex-end;">
            <button type="button" class="btn-submit" onclick="closeViewLeaveModal()">Close</button>
        </div>
    </div>
</div>

{{-- ================= TOAST MESSAGE ================= --}}
<div id="toast" style="position:fixed; bottom:20px; right:20px; background:#ffffff; color:#111111; padding:14px 20px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2); display:none; z-index:9999; font-weight:600; font-size:14px;"></div>

<script>
/* OPEN ADD */
function openLeaveModal() {
    document.getElementById('leaveForm').reset();
    document.getElementById('leave_id').value = '';
    document.getElementById('leaveModalTitle').innerText = 'Add Leave';
    document.getElementById('leaveModal').style.display = 'flex';
}

/* OPEN EDIT */
function openEditLeaveModal(id) {
    fetch(`/employer/leaves/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('leave_id').value = data.id;
            document.getElementById('from_date').value = data.from_date;
            document.getElementById('to_date').value = data.to_date;
            document.getElementById('type').value = data.type;
            document.getElementById('reason').value = data.reason ?? '';
            document.getElementById('leaveModalTitle').innerText = 'Edit Leave';
            document.getElementById('leaveModal').style.display = 'flex';
        });
}

/* CLOSE MODAL */
function closeLeaveModal() {
    document.getElementById('leaveModal').style.display = 'none';
}

/* SUBMIT ADD + EDIT */
document.getElementById('leaveForm').addEventListener('submit', function(e){
    e.preventDefault();
    let id = document.getElementById('leave_id').value;
    let url = id ? `/employer/leaves/${id}` : `/employer/leaves`;
    let method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            from_date: document.getElementById('from_date').value,
            to_date: document.getElementById('to_date').value,
            type: document.getElementById('type').value,
            reason: document.getElementById('reason').value
        })
    })
    .then(res => res.json())
    .then(() => {
        showToast(id ? 'Leave updated successfully' : 'Leave applied successfully');
        closeLeaveModal();
        

        if (typeof refreshTable === "function") {
            refreshTable();
        }
    })
    .catch(err => console.error('Error submitting form:', err));
});

/* DELETE */
function deleteLeave(id){
    if(!confirm('Are you sure you want to delete this leave?')) return;
    fetch(`/employer/leaves/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
        }
    })
    .then(() => {
        showToast('Leave deleted successfully');
        
       
        if (typeof refreshTable === "function") {
            refreshTable();
        }
    })
    .catch(err => console.error('Error deleting record:', err));
}

/* VIEW DETAILS */
function viewLeave(id) {
    fetch(`/employer/leaves/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('view_type').innerText = data.type;
            document.getElementById('view_reason').innerText = data.reason ? data.reason : 'No reason provided';
            document.getElementById('view_from').innerText = data.from_date;
            document.getElementById('view_to').innerText = data.to_date;
            document.getElementById('view_status').innerText = data.status;
            document.getElementById('viewLeaveModal').style.display = 'flex';
        })
        .catch(err => {
            console.error(err);
            alert('Could not load leave details.');
        });
}

function closeViewLeaveModal() {
    document.getElementById('viewLeaveModal').style.display = 'none';
}

/* TOAST */
function showToast(msg){
    let toast = document.getElementById('toast');
    toast.innerText = msg;
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 3000);
}
</script>