@extends('layouts.app')

@section('title', 'Departments')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Departments</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Department Management</h1>
            <p class="text-muted mb-0">Manage organizational departments and structure</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Department
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.departments.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Department name, code, or head name...">
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Departments Grid -->
    <div class="row g-4">
        @forelse($departments as $department)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-building" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.departments.show', $department) }}">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.departments.edit', $department) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item" onclick="toggleDepartmentStatus({{ $department->id }})">
                                    <i class="bi bi-toggle-{{ $department->is_active ? 'on' : 'off' }} me-2"></i>
                                    {{ $department->is_active ? 'Deactivate' : 'Activate' }}
                                </button></li>
                                <li><button class="dropdown-item text-danger" onclick="deleteDepartment({{ $department->id }})">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </button></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h5 class="card-title mb-2">{{ $department->name }}</h5>
                    <p class="text-muted mb-3">
                        <span class="badge bg-secondary me-2">{{ $department->code }}</span>
                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                    </p>
                    
                    @if($department->description)
                        <p class="card-text small text-muted mb-3">{{ Str::limit($department->description, 100) }}</p>
                    @endif
                    
                    @if($department->head_name)
                        <div class="mb-3">
                            <small class="text-muted">Department Head:</small>
                            <div class="fw-semibold">{{ $department->head_name }}</div>
                        </div>
                    @endif
                    
                    <!-- Statistics -->
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-primary">{{ $department->users_count }}</div>
                                <small class="text-muted">Users</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">{{ $department->origin_documents_count }}</div>
                                <small class="text-muted">Origin</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-info">{{ $department->current_documents_count }}</div>
                                <small class="text-muted">Current</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No departments found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status']))
                        Try adjusting your filters or <a href="{{ route('admin.departments.index') }}">clear all filters</a>.
                    @else
                        Create your first department to get started.
                    @endif
                </p>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Department
                </a>
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($departments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $departments->appends(request()->query())->links() }}
    </div>
    @endif
@endsection

@push('scripts')
<script>
    function toggleDepartmentStatus(departmentId) {
        if (confirm('Are you sure you want to change the status of this department?')) {
            fetch(`/api/admin/departments/${departmentId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating department status.');
            });
        }
    }
    
    function deleteDepartment(departmentId) {
        if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/departments/${departmentId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush