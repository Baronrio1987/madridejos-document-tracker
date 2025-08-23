@extends('layouts.app')

@section('title', 'Document Routes')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Document Routes</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Document Routes</h1>
            <p class="text-muted mb-0">Track document routing between departments</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('document-routes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                    </select>
                </div>
                
                <div class="col-md-3">
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
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('document-routes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Routes Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($routes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Document</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Routed Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                            <tr>
                                <td>
                                    <a href="{{ route('documents.show', $route->document) }}" class="text-decoration-none">
                                        {{ $route->document->tracking_number }}
                                    </a>
                                    <div class="small text-muted">{{ Str::limit($route->document->title, 30) }}</div>
                                </td>
                                <td class="text-muted">{{ $route->fromDepartment->name }}</td>
                                <td class="text-muted">{{ $route->toDepartment->name }}</td>
                                <td>{{ Str::limit($route->routing_purpose, 40) }}</td>
                                <td>
                                    <span class="badge bg-{{ $route->status == 'processed' ? 'success' : ($route->status == 'received' ? 'info' : 'warning') }}">
                                        {{ ucfirst($route->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $route->routed_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($route->status === 'pending' && $route->to_department_id === Auth::user()->department_id)
                                            <li><button class="dropdown-item" onclick="receiveRoute({{ $route->id }})">
                                                <i class="bi bi-check me-2"></i>Receive
                                            </button></li>
                                            @endif
                                            @if($route->status === 'received' && $route->to_department_id === Auth::user()->department_id)
                                            <li><button class="dropdown-item" onclick="processRoute({{ $route->id }})">
                                                <i class="bi bi-gear me-2"></i>Process
                                            </button></li>
                                            @endif
                                            <li><a class="dropdown-item" href="{{ route('documents.show', $route->document) }}">
                                                <i class="bi bi-eye me-2"></i>View Document
                                            </a></li>
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
                    {{ $routes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-arrow-left-right text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No routes found</h5>
                    <p class="text-muted">No document routes match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function receiveRoute(routeId) {
        if (confirm('Are you sure you want to receive this document?')) {
            fetch(`/routes/${routeId}/receive`, {
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
            });
        }
    }
    
    function processRoute(routeId) {
        const remarks = prompt('Enter processing remarks (optional):');
        
        fetch(`/routes/${routeId}/process`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ remarks: remarks })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
</script>
@endpush