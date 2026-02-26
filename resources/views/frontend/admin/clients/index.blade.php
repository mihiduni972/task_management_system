@extends('frontend.layouts.app')

@section('content')
<style>
  
    .view-card-label { font-size: 0.75rem; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 5px; display: block; }
    .view-card-value { background-color: #f9f9f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 15px; color: #1a202c; font-weight: 500; min-height: 45px; display: flex; align-items: center; font-size: 0.95rem; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02); }
    .section-header { color: #000; font-size: 0.9rem; font-weight: 800; letter-spacing: 0.8px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
    .modal-custom { border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    
    .pagination-wrapper nav div:first-child, .pagination-wrapper nav p { display: none !important; }
    .pagination-wrapper { display: flex; justify-content: center; margin-top: 25px; }
    .pagination { display: flex !important; list-style: none; padding: 0; margin: 0; }
    .pagination li a, .pagination li span { min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #ffffff !important; color: #000000 !important; border: 1px solid #d1d5db !important; text-decoration: none; font-size: 13px; font-weight: 500; margin-left: -1px; }
    .pagination li.active span { background: #000000 !important; color: #ffffff !important; border-color: #000000 !important; }

    

.toast-container { 
    z-index: 99999 !important;
}

.custom-toast {
    min-width: 260px;             
    padding: 0 !important;
    background: white !important;
    border: none !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 20px rgba(0,0,0,0.12) !important;
}

.toast-body { 
    padding: 4px 8px !important;   
    font-weight: 300;
    color: black;
    font-size: 0.85rem;           
    line-height: 1.2;              
    min-height: 32px;              
    display: flex;
    align-items: center;
}

.toast-body i {
    font-size: 1rem !important;
    margin-right: 8px !important;
}

.custom-toast .btn-close {
    transform: scale(0.8);         
}
</style>

<div class="container-fluid mt-4 px-4">
    <div class="client-header d-flex justify-content-between align-items-center mb-5"> 
        <h2 class="fw-bold m-0">Clients</h2>
        <div class="ms-auto">
            <button class="btn btn-danger" onclick="openAddClientModal()" style="background-color: #7b1b1b; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-plus me-2"></i> Add New Client
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="clientsTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $key => $client)
                    <tr id="clientRow{{ $client->id }}">
                        <td class="ps-4">{{ $key+1 }}</td>
                        <td class="fw-bold">{{ $client->name }}</td>
                        <td>{{ $client->company ?? '-' }}</td>
                        <td>{{ $client->email ?? '-' }}</td>
                        <td>{{ $client->phone ?? '-' }}</td>
                        <td>
                            <span class="badge {{ strtolower($client->status) == 'active' ? 'bg-success-subtle text-success border-success' : 'bg-secondary-subtle text-secondary border-secondary' }} border px-2 py-1">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <button class="btn btn-sm btn-outline-secondary me-1" style="padding: 6px 14px;" onclick="viewClient({{ $client->id }})">View</button>
                            <button class="btn btn-sm btn-outline-secondary me-1" style="padding: 6px 14px;" onclick="editClient({{ $client->id }})">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" style="padding: 6px 14px;" onclick="deleteClient({{ $client->id }})">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No clients found</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-wrapper">{{ $clients->links() }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="clientForm" class="modal-content modal-custom">
            @csrf
            <input type="hidden" id="client_id">
            <div class="modal-header border-0 pt-3 px-4 pb-2"> 
                <h5 class="fw-bold w-100 text-center" id="modalTitle" style="color: #000; font-size: 1.5rem; margin-bottom: 0;">Add Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-0">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Name</label>
                    <input class="form-control" id="name" placeholder="Enter name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Company</label>
                    <input class="form-control" id="company" placeholder="Enter company">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Phone</label>
                    <input class="form-control" id="phone" placeholder="Enter phone number">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Status</label>
                    <select class="form-select" id="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 justify-content-end"> 
                <button type="button" class="btn btn-light border px-4 me-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <button class="btn btn-dark px-4" id="saveBtn" type="submit" style="border-radius: 8px; background-color:#7c1616; color: white;">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="viewClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-custom">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold" style="color: #000; font-size: 1.4rem;">Client Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewClientBody"></div>
            <div class="modal-footer border-0 pb-4 pe-4">
                <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="appToast" class="toast custom-toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center">
                <i id="toastIcon" class="fas fa-check-circle me-3" style="font-size: 1.2rem;"></i>
                <span id="toastMsg"></span>
            </div>
            <button type="button" class="btn-close me-3 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const clientModal = new bootstrap.Modal(document.getElementById('clientModal'));
    const viewModal   = new bootstrap.Modal(document.getElementById('viewClientModal'));

    
    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('appToast');
        const toastMsg = document.getElementById('toastMsg');
        const toastIcon = document.getElementById('toastIcon');
        
        
        toastMsg.innerText = message;
        toastEl.classList.remove('toast-success', 'toast-danger');
        
        if(type === 'success') {
            toastEl.classList.add('toast-success');
            toastIcon.className = 'fas fa-check-circle me-3 text-success';
        } else {
            toastEl.classList.add('toast-danger');
            toastIcon.className = 'fas fa-exclamation-circle me-3 text-danger';
        }

        const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bsToast.show();
    }

    function openAddClientModal() {
        document.getElementById('clientForm').reset();
        document.getElementById('client_id').value = '';
        document.getElementById('modalTitle').innerText = 'Add Client';
        clientModal.show();
    }

    function editClient(id) {
        fetch(`/admin/clients/${id}/edit`)
        .then(r => r.json())
        .then(c => {
            document.getElementById('client_id').value = c.id;
            document.getElementById('name').value = c.name;
            document.getElementById('company').value = c.company ?? '';
            document.getElementById('email').value = c.email ?? '';
            document.getElementById('phone').value = c.phone ?? '';
            document.getElementById('status').value = c.status;
            document.getElementById('modalTitle').innerText = 'Edit Client';
            clientModal.show();
        });
    }

    document.getElementById('clientForm').addEventListener('submit', e => {
        e.preventDefault();
        const id = document.getElementById('client_id').value;
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;

        fetch(id ? `/admin/clients/${id}` : `/admin/clients`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _method: id ? 'PUT' : 'POST',
                name: document.getElementById('name').value,
                company: document.getElementById('company').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                status: document.getElementById('status').value
            })
        })
        .then(async r => {
            const data = await r.json();
            if (!r.ok) throw new Error(data.message || 'Save failed');
            return data;
        })
        .then(data => {
            clientModal.hide();
            showToast(data.message, 'success'); 
            setTimeout(() => location.reload(), 1500); 
        })
        .catch(err => {
            saveBtn.disabled = false;
            showToast(err.message, 'danger');
        });
    });

    function deleteClient(id) {
        if (!confirm('Are you sure you want to delete this client?')) return;
        fetch(`/admin/clients/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1200);
        })
        .catch(err => showToast('Delete failed!', 'danger'));
    }

    function viewClient(id) {
        fetch(`/admin/clients/${id}`)
        .then(r => r.json())
        .then(c => {
            const statusBadge = c.status === 'active' 
                ? '<span class="fw-bold text-success">Active</span>' 
                : '<span class="fw-bold text-secondary">Inactive</span>';

            document.getElementById('viewClientBody').innerHTML = `
                <div class="view-details-container px-2">
                    <div class="detail-row"><span class="detail-label">Full Name</span><span class="detail-value">${c.name}</span></div>
                    <div class="detail-row"><span class="detail-label">Company</span><span class="detail-value">${c.company ?? '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Email Address</span><span class="detail-value">${c.email ?? '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Phone Number</span><span class="detail-value">${c.phone ?? '-'}</span></div>
                    <div class="detail-row border-0"><span class="detail-label">Status</span><span class="detail-value">${statusBadge}</span></div>
                </div>`;
            viewModal.show();
        });
    }
</script>
@endpush