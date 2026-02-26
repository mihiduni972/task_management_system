@extends('frontend.layouts.app')

@section('content')
<style>
    /* UI Design Improvements */
    .btn-danger-custom {
        background-color: #7b1b1b !important;
        color: white !important;
        border-radius: 50px;
        font-weight: 600;
        padding: 8px 20px;
        border: none;
    }

    .btn-search-gray {
        background-color: #f0f0f0;
        color: #333;
        border-radius: 10px;
        font-weight: 520;
        border: none;
        padding: 6px 25px;
        transition: 0.3s;
    }

    .custom-filter-input {
        border-radius: 10px !important;
        border: 1px solid #ced4da !important;
        background-color: #ffffff !important;
        padding: 8px 15px;
        color: #6c757d;
        height: auto;
    }

    .btn-clear-search {
        background-color: #ffffff !important;
        color: #6c757d !important;
        border: 1px solid #ced4da !important;
        border-radius: 10px;
        font-weight: 500;
        padding: 8px 20px;
        text-decoration: none;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-action-gray {
        background-color: #f0f0f0 !important;
        color: #555 !important;
        border-radius: 8px;
        font-weight: 500;
        padding: 4px 10px;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #e0e0e0;
        display: inline-flex;
        align-items: center;
        transition: 0.2s;
        white-space: nowrap;
    }

    .custom-rounded-card {
        border-radius: 15px;
        overflow: hidden;
    }

    .table td, .table th {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .action-wrapper {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap; 
        justify-content: center;
    }

    .table-top-margin {
        margin-top: 15px !important;
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

    .pagination li a:hover {
        background-color: #f3f4f6 !important;
        color: #000000 !important;
    }

   .toast-container {
    z-index: 99999 !important;
}

.custom-toast {
    min-width: 200px;
    max-width: 350px;
    border-radius: 12px !important;
    border: none !important;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    padding: 0 !important;
}

/* Flex wrapper inside toast */
.custom-toast .d-flex {
    padding: 6px 10px !important;   
    align-items: center !important;  
    justify-content: space-between;  
}

/* Message text */
.toast-body {
    font-weight: 500;
    font-size: 0.9rem;
    line-height: 1.3;
    word-break: break-word;
    flex: 1 1 auto;
    margin: 0;
    text-align: center; 
}
</style>

<div class="container-fluid py-4">
    {{-- HEADER SECTION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold text-dark">Assets</h2>
        <div class="header-action-wrapper d-flex gap-2">
            <button type="button" class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
               +  Assign Asset
            </button>
            <button type="button" class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                + Add Asset
            </button>
        </div>
    </div>

    {{-- ASSET TABLE --}}
    <div class="card border-0 shadow-sm custom-rounded-card">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex justify-content-end mb-2"> 
                <form action="{{ route('assets.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                    <input type="text" name="search" class="form-control custom-filter-input" style="max-width: 280px;" placeholder="Search by Asset Name..." value="{{ request('search') }}">
                    <select name="status" class="form-select custom-filter-input" style="max-width: 160px;">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    </select>
                    <button type="submit" class="btn btn-search-red">Search</button>
                    @if(request()->filled('search') || request()->filled('status'))
                        <a href="{{ route('assets.index') }}" class="btn-clear-search">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0 table-top-margin">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-center">Qty</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th class="text-center" style="min-width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $key => $asset)
                        <tr>
                            <td class="text-center text-secondary">{{ $key + 1 }}</td>
                            <td class="fw-bold">{{ $asset->asset_name }}</td>
                            <td>{{ ucfirst($asset->asset_type) }}</td>
                            <td class="text-center">{{ $asset->quantity ?? 1 }}</td>
                            <td>
                                @if($asset->status === 'assigned')
                                    <div class="assigned-user-badge">
                                        <i class="bi bi-person-circle me-2 text-danger"></i>
                                        {{ $asset->current_holder?->username ?? 'N/A' }}
                                    </div>
                                @else
                                    <span class="text-muted small">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $asset->status === 'available' ? 'status-available' : 'status-assigned' }}">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="action-wrapper">
                                    @if($asset->status === 'assigned')
                                        <form action="{{ route('assets.return', $asset->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn-action-gray text-primary border-0">Return</button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn-action-gray border-0" data-bs-toggle="modal" data-bs-target="#editAssetModal{{ $asset->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-gray text-danger border-0">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        {{-- EDIT MODAL --}}
                        <div class="modal fade" id="editAssetModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content shadow-lg border-0 px-3 py-2" style="border-radius: 20px;">
                                    <div class="modal-header border-0 pb-0 d-flex justify-content-between">
                                        <h5 class="fw-bold text-dark m-0">Edit Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-dark d-block">Asset Name</label>
                                                <input type="text" name="asset_name" class="form-control border-light bg-light rounded-3" value="{{ $asset->asset_name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-dark d-block">Asset Type</label>
                                                <select name="asset_type" class="form-select border-light bg-light rounded-3" required>
                                                    <option value="laptop" {{ $asset->asset_type == 'laptop' ? 'selected' : '' }}>Laptop</option>
                                                    <option value="mouse" {{ $asset->asset_type == 'mouse' ? 'selected' : '' }}>Mouse</option>
                                                    <option value="keyboard" {{ $asset->asset_type == 'keyboard' ? 'selected' : '' }}>Keyboard</option>
                                                    <option value="monitor" {{ $asset->asset_type == 'monitor' ? 'selected' : '' }}>Monitor</option>
                                                    <option value="other" {{ $asset->asset_type == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-dark d-block">Quantity</label>
                                                <input type="number" name="quantity" class="form-control border-light bg-light rounded-3" value="{{ $asset->quantity ?? 1 }}" min="1">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-bold small text-dark d-block">Description (Optional)</label>
                                                <textarea name="description" class="form-control border-light bg-light rounded-3" rows="3">{{ $asset->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 justify-content-start px-4 pb-4">
                                            <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger-custom px-4">Update Asset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle me-2"></i> No assets found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                @if($assets->hasPages())
                    <div class="pagination-wrapper">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ASSIGN ASSET MODAL --}}
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 px-3 py-2" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between">
                <h3 class="fw-bold text-dark m-0">Assign Asset</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('assets.assign') }}">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Select Asset</label>
                        <select name="asset_id" class="form-select border-light bg-light rounded-3" required>
                            <option value="">-- Choose Asset --</option>
                            @foreach($assets->where('status', 'available') as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->asset_name }} (Stock: {{ $asset->quantity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Select User</label>
                        <select name="user_id" class="form-select border-light bg-light rounded-3" required>
                            <option value="">-- Choose User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-start px-4 pb-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger-custom px-4">Assign Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ADD ASSET MODAL --}}
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 px-3 py-2" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between">
                <h3 class="fw-bold text-dark m-0" style="margin-top:4px; margin-bottom:4px;">Add New Asset</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Asset Name</label>
                        <input type="text" name="asset_name" class="form-control border-light bg-light rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Asset Type</label>
                        <select name="asset_type" class="form-select border-light bg-light rounded-3" required>
                            <option value="laptop">Laptop</option>
                            <option value="mouse">Mouse</option>
                            <option value="keyboard">Keyboard</option>
                            <option value="monitor">Monitor</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Quantity</label>
                        <input type="number" name="quantity" class="form-control border-light bg-light rounded-3" value="1" min="1">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-dark">Description</label>
                        <textarea name="description" class="form-control border-light bg-light rounded-3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-start px-4 pb-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger-custom px-4">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TOAST MESSAGE --}}
<div id="appToast" class="toast custom-toast" role="alert">
    <div class="toast-content">
        <div class="toast-body text-center" id="toastMsg"></div>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
</div>
@endsection

@push('scripts')
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toastEl = document.getElementById('appToast');
    const toastMsg = document.getElementById('toastMsg');

    @if(session('success'))
        toastMsg.innerText = "{{ session('success') }}";
        toastEl.classList.add('toast-success');
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    @endif

    @if(session('error'))
        toastMsg.innerText = "{{ session('error') }}";
        toastEl.classList.add('toast-error');
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    @endif
});
</script>
@endpush