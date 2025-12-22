@extends('frontend.layouts.app')

@section('content')

<div class="task-card">
    <h2>ADD TASK</h2>

    <form method="POST" action="/tasks/store">
        @csrf

        <label>Task</label>
        <input type="text" name="task_title" required>

        <label>Sub Task</label>
        <input type="text" name="sub_task">

        <label>Status</label>
        <select name="status_id">
            <option value="">Select</option>
            <option value="1">Pending</option>
            <option value="2">In Progress</option>
            <option value="3">Done</option>
        </select>

        <div class="date-row">
            <input type="date" name="start_date">
            <input type="date" name="end_date">
        </div>

        <button type="submit" class="create-btn">CREATE</button>
    </form>
</div>

@endsection
