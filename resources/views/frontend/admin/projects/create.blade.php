@extends('frontend.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
<style>
    /* ================= PROJECT CARD ================= */
    .project-card {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        max-width: 800px;
        width: 100%;
        margin-top: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .btn-submit {
        background: #b22222;
        color: #fff;
        padding: 10px 30px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: #8b1a1a;
        transform: translateY(-2px);
    }

    .hint {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
        display: block;
    }

    .required-star {
        color: #b22222;
    }

    .page-center {
        display: flex;
        justify-content: center;
        padding: 20px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #b22222;
        box-shadow: 0 0 0 0.2rem rgba(178, 34, 34, 0.25);
    }
</style>
@endpush

@section('content')
<div class="page-center">

    <div class="project-card">
        {{-- Page Header --}}
        <div class="d-flex align-items-center mb-4">
            <h2 class="fw-bold mb-0"> Create New Project</h2>
            <a href="{{ route('admin.projects.index') }}"
               class="btn btn-outline-secondary ms-auto">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 mb-4">
                <strong class="d-block mb-2">Whoops! Something went wrong:</strong>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.projects.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Project Name <span class="required-star">*</span>
                    </label>
                    <input type="text" class="form-control shadow-sm"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="e.g. Toyar ERP System"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Client Name</label>
                    <input type="text" class="form-control shadow-sm"
                           name="client_name"
                           value="{{ old('client_name') }}"
                           placeholder="e.g. Toyar Pvt Ltd">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control shadow-sm"
                          name="description"
                          rows="3"
                          placeholder="Describe the project goals...">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control shadow-sm"
                           name="start_date"
                           value="{{ old('start_date') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control shadow-sm"
                           name="end_date"
                           value="{{ old('end_date') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Status <span class="required-star">*</span>
                    </label>
                    <select name="status" class="form-select shadow-sm" required>
  
    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
    <option value="on-hold" {{ old('status') == 'on-hold' ? 'selected' : '' }}>On-Hold</option>
    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
</select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Assign Employers</label>
                    <select name="employers[]" class="form-select shadow-sm"
                            multiple style="height: 110px;">
                        @foreach ($employers as $employer)
                            <option value="{{ $employer->id }}"
                                {{ (is_array(old('employers')) && in_array($employer->id, old('employers'))) ? 'selected' : '' }}>
                                {{ $employer->full_name }} </option>
                        @endforeach
                    </select>
                    <small class="hint">
                        <i class="fas fa-info-circle"></i>
                        Hold <strong>Ctrl</strong> (Windows) or <strong>Command</strong> (Mac) to select multiple
                    </small>
                </div>
            </div>

            <hr class="my-4">

            <div class="text-end">
                <button type="reset" class="btn btn-light px-4 me-2 border">
                    Reset
                </button>
                <button type="submit" class="btn-submit px-5">
                    Save Project
                </button>
            </div>
        </form>
    </div>

</div>
@endsection