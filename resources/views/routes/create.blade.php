@extends('layouts.app')

@section('title', 'Route Document')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
    <li class="breadcrumb-item"><a href="{{ route('documents.show', $document) }}">{{ $document->tracking_number }}</a></li>
    <li class="breadcrumb-item active">Route Document</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Route Document</h1>
            <p class="text-muted mb-0">{{ $document->tracking_number }} - {{ $document->title }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Document
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Routing Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('documents.routing.store', $document) }}" id="routeForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Current Department (Read-only) -->
                            <div class="col-md-6">
                                <label for="from_department" class="form-label">From Department</label>
                                <input type="text" class="form-control-plaintext" id="from_department" 
                                       value="{{ $document->currentDepartment->name }}" readonly>
                            </div>
                            
                            <!-- Target Department -->
                            <div class="col-md-6">
                                <label for="to_department_id" class="form-label">To Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('to_department_id') is-invalid @enderror" 
                                        id="to_department_id" name="to_department_id" required>
                                    <option value="">Select Destination Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('to_department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Routing Purpose -->
                            <div class="col-12">
                                <label for="routing_purpose" class="form-label">Routing Purpose <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('routing_purpose') is-invalid @enderror" 
                                          id="routing_purpose" name="routing_purpose" rows="3" 
                                          placeholder="Specify the purpose for routing this document..." required>{{ old('routing_purpose') }}</textarea>
                                @error('routing_purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Instructions -->
                            <div class="col-12">
                                <label for="instructions" class="form-label">Special Instructions</label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                          id="instructions" name="instructions" rows="3" 
                                          placeholder="Any special instructions for the receiving department...">{{ old('instructions') }}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Routing Templates -->
                        <div class="mt-4">
                            <h6 class="mb-3">Quick Routing Templates</h6>
                            <div class="row g-3">
                                @foreach($templates as $template)
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="card-title mb-2">{{ $template->name }}</h6>
                                            <p class="card-text small text-muted mb-2">{{ $template->description }}</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="useTemplate({{ $template->id }})">
                                                Use Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                
                                @if($templates->count() === 0)
                                <div class="col-12">
                                    <div class="text-center py-3 text-muted">
                                        <i class="bi bi-diagram-3" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">No routing templates available for this document type</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-arrow-right me-2"></i>Route Document
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Document Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Document Details</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Tracking:</span>
                            <span class="fw-semibold">{{ $document->tracking_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Type:</span>
                            <span>{{ $document->documentType->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Priority:</span>
                            <span>
                                <i class="bi bi-flag priority-{{ $document->priority }}"></i>
                                {{ ucfirst($document->priority) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Status:</span>
                            <span class="status-badge status-{{ $document->status }}">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="text-muted">Created:</span>
                            <span>{{ $document->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Routing History -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Routing History</h6>
                </div>
                <div class="card-body">
                    @if($document->routes->count() > 0)
                        <div class="timeline">
                            @foreach($document->routes->sortByDesc('routed_at')->take(3) as $route)
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-{{ $route->status == 'processed' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $route->status == 'processed' ? 'success' : 'warning' }} rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 30px; height: 30px; min-width: 30px;">
                                    <i class="bi bi-arrow-right small"></i>
                                </div>
                                <div class="small">
                                    <div class="fw-semibold">{{ $route->toDepartment->name }}</div>
                                    <div class="text-muted">{{ $route->routed_at->format('M d, Y') }}</div>
                                    <div class="badge bg-{{ $route->status == 'processed' ? 'success' : 'warning' }} bg-opacity-20 text-{{ $route->status == 'processed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($route->status) }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($document->routes->count() > 3)
                            <small class="text-muted">...and {{ $document->routes->count() - 3 }} more routes</small>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-arrow-right" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2 small">No routing history</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Available Departments -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Available Departments</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        @foreach($departments->take(5) as $dept)
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded d-flex align-items-center justify-content-center me-2" 
                                 style="width: 24px; height: 24px;">
                                <i class="bi bi-building small"></i>
                            </div>
                            <span>{{ $dept->name }}</span>
                        </div>
                        @endforeach
                        @if($departments->count() > 5)
                        <small class="text-muted">...and {{ $departments->count() - 5 }} more</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function useTemplate(templateId) {
        fetch(`/routing-templates/${templateId}`)
            .then(response => response.json())
            .then(data => {
                if (data.template && data.departments) {
                    // Get the first department in the sequence (after current)
                    const routeSequence = data.template.route_sequence;
                    const currentDeptId = {{ $document->current_department_id }};
                    const currentIndex = routeSequence.indexOf(currentDeptId);
                    
                    if (currentIndex !== -1 && currentIndex < routeSequence.length - 1) {
                        const nextDeptId = routeSequence[currentIndex + 1];
                        document.getElementById('to_department_id').value = nextDeptId;
                        
                        // Set default routing purpose
                        document.getElementById('routing_purpose').value = `For processing according to ${data.template.name}`;
                    } else {
                        alert('No next department found in this template routing sequence.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading template information.');
            });
    }
    
    // Form validation
    document.getElementById('routeForm').addEventListener('submit', function(e) {
        const fromDept = {{ $document->current_department_id }};
        const toDept = parseInt(document.getElementById('to_department_id').value);
        
        if (fromDept === toDept) {
            e.preventDefault();
            alert('Cannot route document to the same department it is currently in.');
            return false;
        }
    });
</script>
@endpush