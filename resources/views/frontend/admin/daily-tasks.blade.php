@extends('frontend.layouts.app')

@section('content')
<div class="card full-width">
    <h2>All Daily Tasks</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employer</th>
                <th>Task</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $task->user->username }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->date }}</td>
                    <td>{{ $task->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
