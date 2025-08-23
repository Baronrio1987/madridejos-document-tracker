@extends('layouts.app')

@section('title', 'Documents')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Documents</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Documents</h1>
            <p class="text-muted mb-0">Manage and track all documents</p>
        </div>
        <div class="col-auto">
            @can('create', App\Models\Document::class)
            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Document
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('documents.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Tracking #, title, description...">
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="document_type_id" class="form-label">Type</label>
                    <select class="form-select" id="document_type_id" name="document_type_id">
                        <option value="">All Types</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Documents Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tracking Number</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Origin</th>
                                <th>Current Dept.</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                <td>
                                    <a href="{{ route('documents.show', $document) }}" class="text-decoration-none fw-semibold">
                                        {{ $document->tracking_number }}
                                    </a>
                                    @if($document->is_confidential)
                                        <i class="bi bi-shield-lock text-warning ms-1" title="Confidential"></i>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($document->title, 50) }}
                                    @if($document->hasAttachments())
                                        <i class="bi bi-paperclip text-muted ms-1" title="Has attachments"></i>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $document->documentType->name }}</td>
                                <td class="text-muted">{{ $document->originDepartment->name }}</td>
                                <td class="text-muted">{{ $document->currentDepartment->name }}</td>
                                <td>
                                    <span class="status-badge status-{{ $document->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="bi bi-flag priority-{{ $document->priority }}"></i>
                                    {{ ucfirst($document->priority) }}
                                </td>
                                <td class="text-muted">{{ $document->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('documents.show', $document) }}">
                                                <i class="bi bi-eye me-2"></i>View
                                            </a></li>
                                            @can('update', $document)
                                            <li><a class="dropdown-item" href="{{ route('documents.edit', $document) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a></li>
                                            @endcan
                                            @can('route', $document)
                                            {{-- CHANGED: Updated route name from documents.route.create to documents.routing.create --}}
                                            <li><a class="dropdown-item" href="{{ route('documents.routing.create', $document) }}">
                                                <i class="bi bi-arrow-right me-2"></i>Route
                                            </a></li>
                                            @endcan
                                            @can('delete', $document)
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteDocument('{{ $document->id }}')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white border-0">
                    {{ $documents->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No documents found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'priority', 'document_type_id', 'department_id']))
                            Try adjusting your filters or <a href="{{ route('documents.index') }}">clear all filters</a>.
                        @else
                            Create your first document to get started.
                        @endif
                    </p>
                    @can('create', App\Models\Document::class)
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Document
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function deleteDocument(documentId) {
        if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            {{-- CHANGED: Use Laravel route helper instead of hardcoded URL --}}
            form.action = `{{ url('/documents') }}/${documentId}`;
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