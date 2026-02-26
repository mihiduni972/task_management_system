{{-- ================= ATTENDANCE MODAL ================= --}}
<div class="modal-overlay" id="employerAddAttendanceModal" style="display: none;">
    <div class="modal-card">
        {{-- Close Button --}}
        <button type="button" class="close-modal" onclick="closeAttendanceModal()">&times;</button>

        {{-- Dynamic Title --}}
        <h2 class="modal-title" id="attendanceModalTitle">Add Attendance</h2>

        <form id="employerAttendanceForm">
            @csrf
            <input type="hidden" name="attendance_id" id="attendance_id">
            
            {{-- Updated: Full Name Input replaced with Dropdown --}}
            <div class="form-group">
                <label>Select Employee</label>
                <select name="user_id" id="user_id" required>
                    <option value="">-- Select Employee --</option>
                    @if(isset($employers))
                        @foreach($employers as $emp)
                            <option value="{{ $emp->id }}" {{ auth()->id() == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="status" required>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" id="date" value="{{ now()->toDateString() }}" required>
            </div>

            <div class="form-row">
                <div class="form-group half-width">
                    <label>Check In</label>
                    <input type="time" name="check_in" id="check_in">
                </div>

                <div class="form-group half-width">
                    <label>Check Out</label>
                    <input type="time" name="check_out" id="check_out">
                </div>
            </div>

            {{-- Save Button aligned to right --}}
            <div class="form-footer" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn-submit">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
/* Function to handle opening */
function openAttendanceModal() {
    const modal = document.getElementById('employerAddAttendanceModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
    }
}

/* Function to handle closing */
function closeAttendanceModal() {
    const modal = document.getElementById('employerAddAttendanceModal');
    const form = document.getElementById('employerAttendanceForm');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('active');
        if(form) form.reset(); // Close කරද්දී form එක reset කිරීම සුදුසුයි
    }
}

/* Outside click protection */
window.addEventListener('click', function(event) {
    const modal = document.getElementById('employerAddAttendanceModal');
    if (event.target === modal) {
        closeAttendanceModal();
    }
});
</script>