@extends('frontend.layouts.app') {{-- Main layout file --}}

@section('content')

<h2>Search Results for "<span style="color:#7b1b1b">{{ $query }}</span>"</h2>

@php
    $hasResults = $employees->count() || $projects->count() || $tasks->count() || $clients->count() || $leaves->count() || $assets->count();
@endphp

@if(!$hasResults)
    <p>No results found </p>
@endif

{{-- Employees --}}
@if($employees->count())
    <h4>Employees</h4>
    @foreach($employees as $emp)
        <p>
            {{-- Only link if current user is admin --}}
            @if(auth()->user()->user_type === 'admin')
                <a href="{{ route('admin.users.show', $emp->id) }}">
                    {{ $emp->full_name }}
                </a>
            @else
                {{ $emp->full_name }}
            @endif
        </p>
    @endforeach
@endif

{{-- Projects --}}
@if($projects->count())
    <h4>Projects</h4>
    @foreach($projects as $project)
        <p>
            {{ $project->name }}
        </p>
    @endforeach
@endif

{{-- Tasks --}}
@if($tasks->count())
    <h4>Tasks</h4>
    @foreach($tasks as $task)
        <p>
            {{ $task->task }}
        </p>
    @endforeach
@endif

{{-- Clients --}}
@if($clients->count())
    <h4>Clients</h4>
    @foreach($clients as $client)
        <p>{{ $client->name }}</p>
    @endforeach
@endif

{{-- Leaves --}}
@if($leaves->count())
    <h4>Leaves</h4>
    @foreach($leaves as $leave)
        <p>{{ $leave->reason }}</p>
    @endforeach
@endif

{{-- Assets --}}
@if($assets->count())
    <h4>Assets</h4>
    @foreach($assets as $asset)
        <p>{{ $asset->asset_name }}</p>
    @endforeach
@endif

@endsection
