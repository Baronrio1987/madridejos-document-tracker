@extends('layouts.app')

@section('title', 'Edit Document')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
    <li class="breadcrumb-item"><a href="{{ route('documents.show', $document) }}">{{ $document->tracking_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-header')
    <div class="row align-items-start">
        <div class="col-12 col-lg-8">
            <h1 class="h3 mb-2 mb-md-0">Edit Document</h1>
            <p class="text-muted mb-3 mb-md-0">{{ $document->tracking_number }} - {{ $document->title }}</p>
        </div>
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1 me-sm-2"></i>
                    <span class="d-none d-sm-inline">Back to Document</span>
                    <span class="d-sm-none">Back</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                        Document Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('documents.update', $document) }}" id="documentForm" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Tracking Number (Read-only) -->
                            <div class="col-12 col-md-6">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control-plaintext" id="tracking_number" 
                                       value="{{ $document->tracking_number }}" readonly>
                            </div>
                            
                            <!-- Status (Read-only, updated via separate action) -->
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <div class="form-control-plaintext">
                                    <span class="status-badge status-{{ $document->status }}">
                                        <i class="bi bi-{{ $document->status === 'completed' ? 'check-circle' : ($document->status === 'in_progress' ? 'clock' : ($document->status === 'cancelled' ? 'x-circle' : 'hourglass')) }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Title -->
                            <div class="col-12">
                                <label for="title" class="form-label">
                                    Document Title 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $document->title) }}" 
                                       placeholder="Enter document title" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small class="text-muted">Enter a descriptive title for this document</small>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Enter document description">{{ old('description', $document->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small class="text-muted">Provide additional details about the document content</small>
                                </div>
                            </div>
                            
                            <!-- Document Type -->
                            <div class="col-12 col-md-6">
                                <label for="document_type_id" class="form-label">
                                    Document Type 
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('document_type_id') is-invalid @enderror" 
                                        id="document_type_id" name="document_type_id" required>
                                    <option value="">Select Document Type</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                {{ old('document_type_id', $document->document_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Priority -->
                            <div class="col-12 col-md-6">
                                <label for="priority" class="form-label">
                                    Priority 
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority', $document->priority) == 'low' ? 'selected' : '' }}>
                                        <i class="bi bi-flag"></i> Low
                                    </option>
                                    <option value="normal" {{ old('priority', $document->priority) == 'normal' ? 'selected' : '' }}>
                                        <i class="bi bi-flag-fill"></i> Normal
                                    </option>
                                    <option value="high" {{ old('priority', $document->priority) == 'high' ? 'selected' : '' }}>
                                        <i class="bi bi-flag-fill"></i> High
                                    </option>
                                    <option value="urgent" {{ old('priority', $document->priority) == 'urgent' ? 'selected' : '' }}>
                                        <i class="bi bi-exclamation-triangle-fill"></i> Urgent
                                    </option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Target Completion Date -->
                            <div class="col-12 col-md-6">
                                <label for="target_completion_date" class="form-label">Target Completion Date</label>
                                <input type="date" class="form-control @error('target_completion_date') is-invalid @enderror" 
                                       id="target_completion_date" name="target_completion_date" 
                                       value="{{ old('target_completion_date', $document->target_completion_date?->format('Y-m-d')) }}"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('target_completion_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small class="text-muted">Expected completion date for this document</small>
                                </div>
                            </div>
                            
                            <!-- Confidential Checkbox -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Security</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_confidential" name="is_confidential" value="1"
                                           {{ old('is_confidential', $document->is_confidential) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_confidential">
                                        <i class="bi bi-shield-lock text-warning me-1"></i>
                                        Mark as Confidential
                                    </label>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">Restrict access to authorized personnel only</small>
                                </div>
                            </div>
                            
                            <!-- Remarks -->
                            <div class="col-12">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                          id="remarks" name="remarks" rows="3" 
                                          placeholder="Enter any additional remarks">{{ old('remarks', $document->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small class="text-muted">Additional notes or comments about this document</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <!-- Mobile-optimized action buttons -->
                                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                                    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary order-2 order-sm-1">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary order-1 order-sm-2" id="submitBtn">
                                        <i class="bi bi-check-circle me-2"></i>Update Document
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar with document information -->
        <div class="col-xl-4">
            <!-- Document Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        Document Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="info-card border rounded p-2 p-md-3">
                                <div class="small text-muted mb-1">Created</div>
                                <div class="fw-semibold">{{ $document->created_at->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $document->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-card border rounded p-2 p-md-3">
                                <div class="small text-muted mb-1">Last Updated</div>
                                <div class="fw-semibold">{{ $document->updated_at->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $document->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-card border rounded p-2 p-md-3">
                                <div class="small text-muted mb-1">Created By</div>
                                <div class="fw-semibold">{{ $document->creator->name }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-card border rounded p-2 p-md-3">
                                <div class="small text-muted mb-1">Department</div>
                                <div class="fw-semibold">{{ $document->creator->department->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Document Routing -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-arrow-left-right me-2 text-primary"></i>
                        Routing Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="routing-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Origin:</span>
                            <span class="fw-semibold">{{ $document->originDepartment->name ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Current:</span>
                            <span class="fw-semibold">{{ $document->currentDepartment->name ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="text-muted small">Routes:</span>
                            <span class="fw-semibold">{{ $document->routes ? $document->routes->count() : 0 }}</span>
                        </div>
                    </div>
                    
                    <!-- Progress indicator -->
                    @if($document->routes && $document->routes->count() > 0)
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Progress</small>
                            <small class="text-muted">{{ $document->getProgressPercentage() }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $document->getProgressPercentage() }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Attachments -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="bi bi-paperclip me-2 text-primary"></i>
                        Attachments
                    </h6>
                </div>
                <div class="card-body">
                    @if($document->attachments && $document->attachments->count() > 0)
                        <div class="attachments-list">
                            @foreach($document->attachments->take(3) as $attachment)
                            <div class="d-flex align-items-center mb-2 attachment-item">
                                <i class="bi bi-file-earmark text-muted me-2 flex-shrink-0"></i>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fw-medium text-truncate">{{ Str::limit($attachment->original_name, 20) }}</div>
                                    <small class="text-muted">{{ $attachment->getFileSizeHumanAttribute() }}</small>
                                </div>
                            </div>
                            @endforeach
                            @if($document->attachments->count() > 3)
                            <small class="text-muted d-block mt-2">
                                ...and {{ $document->attachments->count() - 3 }} more file(s)
                            </small>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-paperclip" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2 small">No attachments</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Mobile-optimized styles */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .page-header {
            padding: 1rem;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            min-height: 48px; /* Touch-friendly height */
            font-size: 16px; /* Prevent zoom on iOS */
        }
        
        .btn {
            min-height: 48px;
            font-size: 16px;
        }
        
        .info-card {
            transition: transform 0.2s ease;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
        }
        
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
        }
        
        .routing-info {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        
        .attachment-item {
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }
        
        .attachment-item:hover {
            background-color: #f8f9fa;
        }
    }
    
    @media (max-width: 576px) {
        .card-body {
            padding: 0.75rem;
        }
        
        .info-card {
            padding: 0.75rem !important;
        }
        
        .btn {
            padding: 0.75rem 1rem;
        }
        
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    
    /* Status badge styling */
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-in-progress { background-color: #dbeafe; color: #1e40af; }
    .status-completed { background-color: #d1fae5; color: #065f46; }
    .status-cancelled { background-color: #fee2e2; color: #991b1b; }
    .status-archived { background-color: #f3f4f6; color: #374151; }
    
    /* Form validation styling */
    .is-invalid {
        border-color: #dc3545 !important;
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
    }
    
    /* Loading state */
    .btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .btn.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Touch feedback */
    @media (hover: none) and (pointer: coarse) {
        .btn:active {
            transform: scale(0.98);
        }
        
        .info-card:active {
            transform: scale(0.98);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('documentForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // Form validation
        form.addEventListener('submit', function(e) {
            const targetDate = document.getElementById('target_completion_date').value;
            const receivedDate = '{{ $document->date_received->format("Y-m-d") }}';
            
            if (targetDate && receivedDate) {
                if (new Date(targetDate) <= new Date(receivedDate)) {
                    e.preventDefault();
                    showAlert('error', 'Target completion date must be after the date received.');
                    return false;
                }
            }
            
            // Show loading state
            setLoadingState(true);
        });
        
        // Track changes
        let formChanged = false;
        const formInputs = document.querySelectorAll('#documentForm input, #documentForm select, #documentForm textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                formChanged = true;
                updateSaveButtonState();
            });
            
            // Real-time validation
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
        
        // Warn before leaving if form has changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
        
        // Don't warn when submitting
        form.addEventListener('submit', function() {
            formChanged = false;
        });
        
        // Update save button state
        function updateSaveButtonState() {
            if (formChanged) {
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Save Changes';
            }
        }
        
        // Field validation
        function validateField(field) {
            const value = field.value.trim();
            let isValid = true;
            let message = '';
            
            // Required field validation
            if (field.hasAttribute('required') && !value) {
                isValid = false;
                message = 'This field is required.';
            }
            
            // Specific field validations
            switch (field.id) {
                case 'title':
                    if (value && (value.length < 3 || value.length > 255)) {
                        isValid = false;
                        message = 'Title must be between 3 and 255 characters.';
                    }
                    break;
                    
                case 'target_completion_date':
                    if (value && new Date(value) <= new Date()) {
                        isValid = false;
                        message = 'Target completion date must be in the future.';
                    }
                    break;
            }
            
            // Update field state
            if (isValid) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                hideFieldError(field);
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
                showFieldError(field, message);
            }
        }
        
        function showFieldError(field, message) {
            let feedback = field.parentElement.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentElement.appendChild(feedback);
            }
            feedback.textContent = message;
        }
        
        function hideFieldError(field) {
            const feedback = field.parentElement.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = '';
            }
        }
        
        function setLoadingState(loading) {
            if (loading) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Update Document';
            }
        }
        
        function showAlert(type, message) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }
        
        // Touch feedback for mobile
        if ('ontouchstart' in window) {
            document.querySelectorAll('.btn, .form-control, .form-select').forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                }, { passive: true });
                
                element.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 100);
                }, { passive: true });
            });
        }
        
        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Reset loading state if page becomes visible again
                setLoadingState(false);
            }
        });
    });
</script>
@endpush