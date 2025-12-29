@extends('frontend.layouts.app')


@section('content')
<div class="task-card">

    <h2>ADMIN TASKS</h2>

    <label>Task</label>
    <input type="text" placeholder="Enter task">

    <label>Sub Task</label>
    <input type="text" placeholder="Enter sub task">

    <label>Status</label>
    <select>
        <option>Pending</option>
        <option>Completed</option>
    </select>

    <div class="date-row">
        <div class="date-field">
            <label>Start Date</label>
            <input type="date">
        </div>
        <div class="date-field">
            <label>End Date</label>
            <input type="date">
        </div>
    </div>

    <button class="create-btn">CREATE</button>

</div>
@endsection
