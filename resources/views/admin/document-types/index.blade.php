@extends('layouts.app')

@section('title', 'Document Types')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Document Types</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Document Type Management</h1>
            <p class="text-muted mb-0">Manage document types and their configurations</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.document-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Document Type
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.document-types.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Document type name or code...">
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
                        <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Document Types Grid -->
    <div class="row g-4">
        @forelse($documentTypes as $documentType)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-tags" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.document-types.show', $documentType) }}">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.document-types.edit', $documentType) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item" onclick="toggleDocumentTypeStatus('{{ $documentType->id }}')">
                                    <i class="bi bi-toggle-{{ $documentType->is_active ? 'on' : 'off' }} me-2"></i>
                                    {{ $documentType->is_active ? 'Deactivate' : 'Activate' }}
                                </button></li>
                                <li><button class="dropdown-item text-danger" onclick="deleteDocumentType('{{ $documentType->id }}')">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </button></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h5 class="card-title mb-2">{{ $documentType->name }}</h5>
                    <p class="text-muted mb-3">
                        <span class="badge bg-secondary me-2">{{ $documentType->code }}</span>
                        {{ $documentType->is_active ? 'Active' : 'Inactive' }}
                    </p>
                    
                    @if($documentType->description)
                        <p class="card-text small text-muted mb-3">{{ Str::limit($documentType->description, 100) }}</p>
                    @endif
                    
                    <div class="mb-3">
                        <small class="text-muted">Retention Period:</small>
                        <div class="fw-semibold">{{ $documentType->retention_period }} days</div>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-primary">{{ $documentType->documents_count }}</div>
                                <small class="text-muted">Documents</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">{{ $documentType->routing_templates_count }}</div>
                                <small class="text-muted">Templates</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No document types found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status']))
                        Try adjusting your filters or <a href="{{ route('admin.document-types.index') }}">clear all filters</a>.
                    @else
                        Create your first document type to get started.
                    @endif
                </p>
                <a href="{{ route('admin.document-types.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Document Type
                </a>
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($documentTypes->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $documentTypes->appends(request()->query())->links() }}
    </div>
    @endif
@endsection

@push('scripts')
<script>
    function toggleDocumentTypeStatus(documentTypeId) {
        if (confirm('Are you sure you want to change the status of this document type?')) {
            fetch(`/api/admin/document-types/${documentTypeId}/toggle-status`, {
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
                alert('An error occurred while updating document type status.');
            });
        }
    }
    
    function deleteDocumentType(documentTypeId) {
        if (confirm('Are you sure you want to delete this document type? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/document-types/${documentTypeId}`;
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