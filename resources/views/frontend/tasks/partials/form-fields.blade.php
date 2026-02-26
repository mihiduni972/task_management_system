<div class="form-group">
    <label>Task</label>
    <input type="text"
           name="task"
           value="{{ old('task') }}"
           placeholder="Enter main task name"
           required>
</div>

<div class="form-group">
    <label>Sub Task</label>
    <input type="text"
           name="sub_task"
           value="{{ old('sub_task') }}"
           placeholder="Enter sub task details (optional)">
</div>

<div class="form-group">
    <label>Status</label>
    <select name="status">
        <option value="Pending"
            {{ (isset($task) && $task->status === 'Pending') ? 'selected' : '' }}>
            Pending
        </option>
        <option value="Completed"
            {{ (isset($task) && $task->status === 'Completed') ? 'selected' : '' }}>
            Completed
        </option>
    </select>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Start Date</label>
        <input type="date"
               name="start_date"
               value="{{ $task->start_date ?? '' }}"
               required>
    </div>

    <div class="form-group">
        <label>End Date</label>
        <input type="date"
               name="end_date"
               value="{{ $task->end_date ?? '' }}"
               required>
    </div>
</div>
