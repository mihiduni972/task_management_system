@extends('frontend.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Table Layout Fixes */
    .toyar-table th:nth-child(4),
    .toyar-table td:nth-child(4) {
        max-width: 90px; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .toyar-table .action-center {
        min-width: 300px !important; 
        text-align: center;
        white-space: nowrap;
    }

    .action-center .btn-action, 
    .action-center .btn-log-time, 
    .action-center form {
        display: inline-block !important;
        vertical-align: middle;
        margin: 2px;
    }

    .btn-save {
    background: #7c1616 !important;
    color: #ffffff !important;
    width: auto !important;
    padding: 10px 26px !important;
    border-radius: 10px !important;
    border: none !important;
    font-weight: 100 !important;
    cursor: pointer;
    transition: 0.2s;
    display: block !important;
    margin-left: auto !important;
}
    .custom-modal {
        display: none; 
        position: fixed;
        z-index: 1000;
        left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
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

    div > p.text-sm {
        display: none;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        padding: 16px 20px;
    }

    .btn-cancel,
    .btn-save {
        flex: 1;                
        padding: 12px 0;        
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        text-align: center;     
    }

    .btn-cancel {
        background: #f1f1f1;
        color: #333;
    }

    .btn-save {
        background: #7b1b1b;
        color: #fff;
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div id="successToast" 
         style="position: fixed; bottom: 20px; right: 20px; background: #fff; color: black; padding: 14px 18px; border-radius: 10px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); font-weight: 500; z-index: 9999; transition: opacity 0.4s ease;">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(function () {
            let toast = document.getElementById('successToast');
            if(toast){
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 400);
            }
        }, 3000);
    </script>
@endif

<div class="page-header">
    <h2>Daily Tasks</h2>
    <button class="btn btn-danger" onclick="openTaskModal()">+ Add Task</button>
</div>

<div class="table-wrapper">
    <table class="toyar-table modern-table daily-tasks-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Project</th>
                <th>Task</th>
                <th>Sub Task</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th class="action-center">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($tasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td>{{ $task->project->name ?? 'N/A' }}</td>
                <td title="{{ $task->task }}">{{ $task->task }}</td>
                <td title="{{ $task->sub_task ?? '-' }}">{{ $task->sub_task ?? '-' }}</td>
                <td>{{ $task->start_date }}</td>
                <td>{{ $task->end_date }}</td>
                <td>
                    {{-- Badge එක ඉවත් කර සාමාන්‍ය Text එක පමණක් දමා ඇත --}}
                    {{ $task->status }}
                </td>
                <td class="action-center">
                    <button class="btn-log-time" data-bs-toggle="modal" data-bs-target="#logTimeModal{{ $task->id }}">
                        Log Time
                    </button>
                    
                    <button class="btn-action view" onclick="openViewTask('{{ $task->project->name ?? 'N/A' }}', '{{ addslashes($task->task) }}', '{{ addslashes($task->sub_task ?? '-') }}', '{{ $task->start_date }}', '{{ $task->end_date }}', '{{ $task->status }}')">
                        View
                    </button>
                    
                    <button class="btn-action edit" onclick="openEditTask({{ $task->id }})">
                        Edit
                    </button>
                    
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action delete" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>

            {{-- Log Time Modal --}}
            <div class="modal fade logtime-modal" id="logTimeModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered task-modal-center">
                    <div class="modal-content" style="position: relative; border-radius: 20px; border: none; overflow: hidden;"> 
                        <div class="modal-header" style="border-bottom: none; padding: 25px 25px 10px 25px; display: block;">
                            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #2d3748;">Log Work Hours</h3>
                            <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 18px; cursor: pointer;">✕</button>
                        </div>

                        <form action="{{ route('time-logs.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            
                            <div class="modal-body px-4 pt-0">
                                <div class="task-info-box" style="padding: 12px 18px; margin-bottom: 20px; background-color: #f0f7ff; border-radius: 12px; border: 1px solid #e1efff;">
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #2d3748;">
                                        {{ $task->project->name ?? 'General' }} | {{ $task->task }}
                                    </p>
                                </div>

                                <div class="task-form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <div class="task-form-group" style="flex: 1;">
                                        <label class="task-label" style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Hours</label>
                                        <select name="hours" class="task-select" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; outline: none;" required>
                                            @for ($i = 0; $i <= 12; $i++) 
                                                <option value="{{ $i }}">{{ $i }} hr</option> 
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="task-form-group" style="flex: 1;">
                                        <label class="task-label" style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Minutes</label>
                                        <select name="minutes" class="task-select" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; outline: none;" required>
                                            <option value="0">00 min</option>
                                            <option value="15">15 min</option>
                                            <option value="30">30 min</option>
                                            <option value="45">45 min</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="task-form-group" style="margin-bottom: 15px;">
                                    <label class="task-label" style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Date</label>
                                    <input type="date" name="log_date" class="task-input" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; outline: none; box-sizing: border-box;" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="task-form-group" style="margin-bottom: 15px;">
                                    <label class="task-label" style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 6px; color: #4a5568;">Description</label>
                                    <textarea name="description" rows="3" class="task-input" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; outline: none; box-sizing: border-box; resize: none;" placeholder="Briefly describe your work..."></textarea>
                                </div>
                            </div>

                           <div class="modal-footer" style="border-top: none; padding: 0 25px 25px 25px; display: block;">
    <button type="submit" class="btn-save">Save</button>
</div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <tr><td colspan="8" class="text-center">No tasks found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $tasks->links() }}
    </div>
</div>

{{-- 1. Add Task Modal --}}
<div id="addTaskModal" class="custom-modal">
    <div class="task-card">
        <h3 class="card-title">Add New Task</h3>
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="ut-form-group">
                <label class="ut-label">Project</label>
                <select name="project_id" class="ut-select" required>
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            @include('frontend.tasks.partials.form-fields')
            <div style="margin-top: 20px;">
                <button type="submit" class="ut-btn-submit">Create Task</button>
            </div>
            <button type="button" class="modal-close" onclick="closeTaskModal()">✕</button>
        </form>
    </div>
</div>

{{-- 2. Edit Task Modal --}}
<div id="editTaskModal" class="custom-modal">
    <div class="task-card"> 
        <h3 class="card-title">Update Task</h3>
        <form id="editTaskForm">
            @csrf @method('PUT')
            <input type="hidden" id="edit_id">
            <div class="ut-form-group">
                <label class="ut-label">Project</label>
                <select id="edit_project_id" class="ut-select" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ut-form-group">
                <label class="ut-label">Task</label>
                <input type="text" id="edit_task" class="ut-input" required>
            </div>
            <div class="ut-form-group">
                <label class="ut-label">Sub Task</label>
                <input type="text" id="edit_sub_task" class="ut-input">
            </div>
            <div class="ut-form-group">
                <label class="ut-label">Status</label>
                <select id="edit_status" class="ut-select">
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="ut-form-row">
                <div class="ut-form-group">
                    <label class="ut-label">Start Date</label>
                    <input type="date" id="edit_start_date" class="ut-input">
                </div>
                <div class="ut-form-group">
                    <label class="ut-label">End Date</label>
                    <input type="date" id="edit_end_date" class="ut-input">
                </div>
            </div>
            <div style="margin-top: 20px;">
                <button type="submit" class="ut-btn-submit">Update</button>
            </div>
            <button type="button" class="modal-close" onclick="closeEditModal()">✕</button>
        </form>
    </div>
</div>

{{-- 3. View Task Modal --}}
<div id="viewTaskModal" class="custom-modal">
    <div class="task-card">
        <button type="button" class="modal-close" onclick="closeViewModal()">✕</button>
        <h3 class="card-title">Task Overview</h3>
        <div class="daily-view-body">
            <div class="task-info-box" style="margin-bottom: 20px;">
                <div class="summary-label">Project Name</div>
                <div class="summary-project-name" id="vProject"></div>
            </div>
            <div class="detail-item-dashed">
                <span class="detail-label">Task</span>
                <span class="detail-value" id="vTask"></span>
            </div>
            <div class="detail-item-dashed">
                <span class="detail-label">Sub Task</span>
                <span class="detail-value" id="vSubTask"></span>
            </div>
            <div class="detail-item-dashed">
                <span class="detail-label">Start Date</span>
                <span class="detail-value" id="vStart"></span>
            </div>
            <div class="detail-item-dashed">
                <span class="detail-label">End Date</span>
                <span class="detail-value" id="vEnd"></span>
            </div>
            <div class="detail-item-dashed" style="border-bottom: none;">
                <span class="detail-label">Status</span>
                {{-- Badge එක ඉවත් කර සාමාන්‍ය Text එකක් ලෙස පෙන්වීමට සකස් කළා --}}
                <span class="detail-value" id="vStatusText"></span>
            </div>
        </div>
        <div style="margin-top: 25px;">
            <button type="button" class="btn-cancel" style="width: 100%;" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
const addTaskM = document.getElementById('addTaskModal');
const editTaskM = document.getElementById('editTaskModal');
const viewTaskM = document.getElementById('viewTaskModal');

function openTaskModal(){ addTaskM.style.display='flex'; }
function closeTaskModal(){ addTaskM.style.display='none'; }
function closeEditModal(){ editTaskM.style.display='none'; }
function closeViewModal(){ viewTaskM.style.display='none'; }

function openViewTask(project, task, sub, start, end, status){
    document.getElementById('vProject').innerText = project;
    document.getElementById('vTask').innerText = task;
    document.getElementById('vSubTask').innerText = sub;
    document.getElementById('vStart').innerText = start;
    document.getElementById('vEnd').innerText = end;
    
    
    document.getElementById('vStatusText').innerText = status;

    viewTaskM.style.display='flex';
}

function openEditTask(id){
    fetch(`/tasks/${id}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(t => {
        document.getElementById('edit_id').value = t.id;
        document.getElementById('edit_project_id').value = t.project_id;
        document.getElementById('edit_task').value = t.task;
        document.getElementById('edit_sub_task').value = t.sub_task ?? '';
        document.getElementById('edit_start_date').value = t.start_date;
        document.getElementById('edit_end_date').value = t.end_date;
        document.getElementById('edit_status').value = t.status;
        editTaskM.style.display='flex';
    });
}

document.getElementById('editTaskForm').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('edit_id').value;

    fetch(`/tasks/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            _method: 'PUT',
            project_id: document.getElementById('edit_project_id').value,
            task: document.getElementById('edit_task').value,
            sub_task: document.getElementById('edit_sub_task').value,
            start_date: document.getElementById('edit_start_date').value,
            end_date: document.getElementById('edit_end_date').value,
            status: document.getElementById('edit_status').value
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            editTaskM.style.display = 'none';
            const toast = document.createElement('div');
            toast.innerText = data.message;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#ffffff';
            toast.style.color = '#000';
            toast.style.padding = '14px 18px';
            toast.style.borderRadius = '10px';
            toast.style.boxShadow = '0 6px 18px rgba(0,0,0,0.2)';
            toast.style.fontWeight = '500';
            toast.style.zIndex = '9999';
            toast.style.transition = 'opacity 0.4s ease';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => { location.reload(); }, 400);
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong.');
    });
});
</script>
@endsection