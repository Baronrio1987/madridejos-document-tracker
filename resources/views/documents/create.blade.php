@extends('layouts.app')

@section('title', 'Create Document')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
    <li class="breadcrumb-item active">Create Document</li>
@endsection

@section('page-header')
    <div class="row align-items-start">
        <div class="col-12 col-lg-8">
            <h1 class="h3 mb-2 mb-lg-0">Create New Document</h1>
            <p class="text-muted mb-3 mb-lg-0">Add a new document to the tracking system</p>
        </div>
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-lg-end">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    <span class="d-none d-sm-inline">Back to Documents</span>
                    <span class="d-sm-none">Back</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Mobile-first form styles */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            min-height: 48px;
            font-size: 16px; /* Prevent zoom on iOS */
        }
        
        .btn {
            min-height: 44px;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 0.75rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
        
        .card-header {
            padding: 1rem 0.75rem;
        }
        
        .h3 {
            font-size: 1.5rem;
        }
    }
    
    /* Enhanced form styling */
    .form-floating-enhanced {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .form-floating-enhanced .form-control {
        border-radius: 0.75rem;
        border: 2px solid #e2e8f0;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-floating-enhanced .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.1);
        transform: translateY(-1px);
    }
    
    .form-floating-enhanced label {
        padding: 0 0.5rem;
        background: white;
        border-radius: 0.25rem;
        font-weight: 600;
        color: #64748b;
    }
    
    .required-field::after {
        content: ' *';
        color: #dc3545;
        font-weight: bold;
    }
    
    /* Sidebar cards mobile optimization */
    .info-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 576px) {
        .info-card {
            margin-bottom: 1rem;
        }
    }
    
    /* Step indicator for mobile */
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 1rem;
        backdrop-filter: blur(10px);
    }
    
    .step {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: #e2e8f0;
        border-radius: 0.5rem;
        margin: 0 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s ease;
    }
    
    .step.active {
        background: var(--bs-primary);
        color: white;
        transform: scale(1.05);
    }
    
    .step.completed {
        background: var(--bs-success);
        color: white;
    }
    
    @media (max-width: 576px) {
        .step {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
    }
    
    /* Auto-save indicator */
    .auto-save-indicator {
        position: fixed;
        top: 1rem;
        right: 1rem;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        z-index: 1050;
        transform: translateY(-100px);
        transition: transform 0.3s ease;
    }
    
    .auto-save-indicator.show {
        transform: translateY(0);
    }
    
    @media (max-width: 576px) {
        .auto-save-indicator {
            top: 0.5rem;
            right: 0.5rem;
            left: 0.5rem;
            text-align: center;
        }
    }
    
    /* Form validation styling */
    .was-validated .form-control:valid {
        border-color: var(--bs-success);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 2.94-2.94.94.94L4.2 6.72z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    /* Touch-friendly buttons */
    .btn-group-mobile {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    @media (min-width: 576px) {
        .btn-group-mobile {
            flex-direction: row;
            gap: 0.5rem;
        }
    }
    
    /* Progress bar for form completion */
    .form-progress {
        position: sticky;
        top: 0;
        z-index: 1020;
        background: white;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1rem;
    }
    
    .progress-bar-smooth {
        transition: width 0.5s ease;
    }
</style>
@endpush

@section('content')
    <!-- Auto-save indicator -->
    <div class="auto-save-indicator" id="autoSaveIndicator">
        <i class="bi bi-cloud-check me-2"></i>
        <span id="saveStatus">Auto-saved</span>
    </div>

    <!-- Form progress indicator -->
    <div class="form-progress">
        <div class="container-fluid">
            <div class="progress" style="height: 4px;">
                <div class="progress-bar progress-bar-smooth bg-primary" role="progressbar" style="width: 0%" id="formProgress"></div>
            </div>
            <small class="text-muted">Form completion: <span id="progressPercent">0%</span></small>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-plus me-2 text-primary"></i>
                        Document Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('documents.store') }}" id="documentForm" novalidate>
                        @csrf
                        
                        <!-- Step indicator for mobile -->
                        <div class="step-indicator d-md-none">
                            <div class="step active" id="step1">
                                <i class="bi bi-1-circle me-1"></i>Basic
                            </div>
                            <div class="step" id="step2">
                                <i class="bi bi-2-circle me-1"></i>Details
                            </div>
                            <div class="step" id="step3">
                                <i class="bi bi-3-circle me-1"></i>Review
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-12">
                                <label for="title" class="form-label required-field">Document Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Enter a clear, descriptive title" 
                                       required
                                       maxlength="255"
                                       data-validation="required">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Choose a clear title that describes the document purpose
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Provide additional details about the document..."
                                          maxlength="1000"
                                          data-validation="optional">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="descriptionCount">0</span>/1000 characters
                                </div>
                            </div>
                            
                            <!-- Document Type and Origin Department -->
                            <div class="col-md-6">
                                <label for="document_type_id" class="form-label required-field">Document Type</label>
                                <select class="form-select @error('document_type_id') is-invalid @enderror" 
                                        id="document_type_id" name="document_type_id" 
                                        required
                                        data-validation="required">
                                    <option value="">Select Document Type</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                data-retention="{{ $type->retention_period ?? 7 }}"
                                                {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="origin_department_id" class="form-label required-field">Origin Department</label>
                                <select class="form-select @error('origin_department_id') is-invalid @enderror" 
                                        id="origin_department_id" name="origin_department_id" 
                                        required
                                        data-validation="required">
                                    <option value="">Select Origin Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ old('origin_department_id', Auth::user()->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('origin_department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Priority and Date Received -->
                            <div class="col-md-6">
                                <label for="priority" class="form-label required-field">Priority Level</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" 
                                        required
                                        data-validation="required">
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        🟢 Low - Routine processing
                                    </option>
                                    <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>
                                        🟡 Normal - Standard processing
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        🟠 High - Important document
                                    </option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                        🔴 Urgent - Immediate attention
                                    </option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="date_received" class="form-label required-field">Date Received</label>
                                <input type="date" class="form-control @error('date_received') is-invalid @enderror" 
                                       id="date_received" name="date_received" 
                                       value="{{ old('date_received', date('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}"
                                       required
                                       data-validation="required">
                                @error('date_received')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Target Completion Date -->
                            <div class="col-md-6">
                                <label for="target_completion_date" class="form-label">Target Completion Date</label>
                                <input type="date" class="form-control @error('target_completion_date') is-invalid @enderror" 
                                       id="target_completion_date" name="target_completion_date" 
                                       value="{{ old('target_completion_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       data-validation="optional">
                                @error('target_completion_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="completionHint"></div>
                            </div>
                            
                            <!-- Confidential Checkbox -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_confidential" name="is_confidential" value="1"
                                           {{ old('is_confidential') ? 'checked' : '' }}
                                           data-validation="optional">
                                    <label class="form-check-label fw-semibold" for="is_confidential">
                                        <i class="bi bi-shield-lock text-warning me-2"></i>
                                        Mark as Confidential
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Confidential documents have restricted access and limited visibility
                                </small>
                            </div>
                            
                            <!-- Remarks -->
                            <div class="col-12">
                                <label for="remarks" class="form-label">Additional Remarks</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                          id="remarks" name="remarks" rows="3" 
                                          placeholder="Add any additional context, instructions, or notes..."
                                          maxlength="500"
                                          data-validation="optional">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="remarksCount">0</span>/500 characters
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="remarks" class="form-label">Requesters Name:</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                          id="remarks" name="remarks" rows="3" 
                                          placeholder="Add Requestors Name.."
                                          maxlength="500"
                                          data-validation="optional">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="remarksCount">0</span>/500 characters
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3">
                                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary order-2 order-sm-1">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <div class="btn-group-mobile order-1 order-sm-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">
                                            <i class="bi bi-save me-2"></i>
                                            <span class="d-none d-sm-inline">Save as Draft</span>
                                            <span class="d-sm-none">Draft</span>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="bi bi-check-circle me-2"></i>Create Document
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Sidebar -->
        <div class="col-12 col-xl-4">
            <!-- Guidelines -->
            <div class="card info-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>Best Practices
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <strong>Clear Titles</strong>
                                <small class="text-muted d-block">Use specific, descriptive titles</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <strong>Correct Type</strong>
                                <small class="text-muted d-block">Select the most appropriate document type</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <strong>Target Dates</strong>
                                <small class="text-muted d-block">Set realistic completion deadlines</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <strong>Privacy Settings</strong>
                                <small class="text-muted d-block">Mark confidential documents appropriately</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Document Types Quick Reference -->
            <div class="card info-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-tags me-2 text-primary"></i>Document Types
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="small">
                        @foreach($documentTypes->take(6) as $type)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-medium">{{ $type->name }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark">{{ $type->code }}</span>
                                @if($type->retention_period)
                                    <small class="text-muted">{{ $type->retention_period }}d</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        @if($documentTypes->count() > 6)
                        <div class="text-center mt-2">
                            <small class="text-muted">...and {{ $documentTypes->count() - 6 }} more types</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Priority Levels Guide -->
            <div class="card info-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-flag me-2 text-info"></i>Priority Levels
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="small">
                        <div class="d-flex align-items-start mb-3">
                            <span class="me-2">🟢</span>
                            <div>
                                <strong class="text-success">Low Priority</strong>
                                <small class="text-muted d-block">Routine documents with flexible deadlines</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <span class="me-2">🟡</span>
                            <div>
                                <strong class="text-warning">Normal Priority</strong>
                                <small class="text-muted d-block">Standard processing timeframe</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <span class="me-2">🟠</span>
                            <div>
                                <strong class="text-orange">High Priority</strong>
                                <small class="text-muted d-block">Important documents requiring prompt attention</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <span class="me-2">🔴</span>
                            <div>
                                <strong class="text-danger">Urgent Priority</strong>
                                <small class="text-muted d-block">Critical documents needing immediate action</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation and auto-save setup
    const form = document.getElementById('documentForm');
    const formInputs = form.querySelectorAll('input, select, textarea');
    const submitBtn = document.getElementById('submitBtn');
    const autoSaveIndicator = document.getElementById('autoSaveIndicator');
    const progressBar = document.getElementById('formProgress');
    const progressPercent = document.getElementById('progressPercent');
    
    let autoSaveTimeout;
    let formChanged = false;
    
    // Character counters
    setupCharacterCounters();
    
    // Form progress tracking
    updateFormProgress();
    
    // Auto-save functionality
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            formChanged = true;
            updateFormProgress();
            
            // Debounced auto-save
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                if (formChanged) {
                    autoSaveForm();
                }
            }, 2000);
        });
        
        input.addEventListener('change', function() {
            updateFormProgress();
        });
    });
    
    // Document type change handler
    document.getElementById('document_type_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const retentionPeriod = selectedOption.dataset.retention || 7;
        
        if (this.value) {
            const today = new Date();
            const targetDate = new Date(today.getTime() + (parseInt(retentionPeriod) * 24 * 60 * 60 * 1000));
            
            document.getElementById('target_completion_date').value = targetDate.toISOString().split('T')[0];
            document.getElementById('completionHint').innerHTML = 
                `<i class="bi bi-info-circle me-1"></i>Suggested: ${retentionPeriod} days from today`;
        }
    });
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            showValidationErrors();
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        
        // Clear auto-saved data on successful submit
        clearAutoSavedData();
    });
    
    // Date validation
    document.getElementById('target_completion_date').addEventListener('change', function() {
        const receivedDate = document.getElementById('date_received').value;
        const targetDate = this.value;
        
        if (targetDate && receivedDate) {
            if (new Date(targetDate) <= new Date(receivedDate)) {
                this.setCustomValidity('Target completion date must be after the date received.');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        }
    });
    
    // Load auto-saved data
    loadAutoSavedData();
    
    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    function setupCharacterCounters() {
        const descriptionField = document.getElementById('description');
        const remarksField = document.getElementById('remarks');
        
        descriptionField.addEventListener('input', function() {
            document.getElementById('descriptionCount').textContent = this.value.length;
        });
        
        remarksField.addEventListener('input', function() {
            document.getElementById('remarksCount').textContent = this.value.length;
        });
        
        // Initial count
        document.getElementById('descriptionCount').textContent = descriptionField.value.length;
        document.getElementById('remarksCount').textContent = remarksField.value.length;
    }
    
    function updateFormProgress() {
        const requiredFields = form.querySelectorAll('[data-validation="required"]');
        let completedFields = 0;
        
        requiredFields.forEach(field => {
            if (field.value.trim() !== '') {
                completedFields++;
            }
        });
        
        const progress = Math.round((completedFields / requiredFields.length) * 100);
        progressBar.style.width = progress + '%';
        progressPercent.textContent = progress + '%';
        
        // Update step indicators on mobile
        updateStepIndicators(progress);
    }
    
    function updateStepIndicators(progress) {
        const steps = document.querySelectorAll('.step');
        if (steps.length === 0) return;
        
        steps.forEach(step => {
            step.classList.remove('active', 'completed');
        });
        
        if (progress < 50) {
            document.getElementById('step1')?.classList.add('active');
        } else if (progress < 90) {
            document.getElementById('step1')?.classList.add('completed');
            document.getElementById('step2')?.classList.add('active');
        } else {
            document.getElementById('step1')?.classList.add('completed');
            document.getElementById('step2')?.classList.add('completed');
            document.getElementById('step3')?.classList.add('active');
        }
    }
    
    function autoSaveForm() {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            localStorage.setItem('document_form_autosave', JSON.stringify({
                data: data,
                timestamp: Date.now()
            }));
            
            showAutoSaveStatus('Auto-saved', 'success');
            formChanged = false;
        } catch (error) {
            console.warn('Could not auto-save form data:', error);
        }
    }
    
    function loadAutoSavedData() {
        try {
            const saved = localStorage.getItem('document_form_autosave');
            if (saved) {
                const { data, timestamp } = JSON.parse(saved);
                
                // Only load if saved within last 24 hours
                if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
                    Object.keys(data).forEach(key => {
                        const field = form.querySelector(`[name="${key}"]`);
                        if (field && !field.value) {
                            if (field.type === 'checkbox') {
                                field.checked = data[key] === '1';
                            } else {
                                field.value = data[key];
                            }
                        }
                    });
                    
                    updateFormProgress();
                    setupCharacterCounters();
                }
            }
        } catch (error) {
            console.warn('Could not load auto-saved data:', error);
        }
    }
    
    function clearAutoSavedData() {
        try {
            localStorage.removeItem('document_form_autosave');
        } catch (error) {
            console.warn('Could not clear auto-saved data:', error);
        }
    }
    
    function showAutoSaveStatus(message, type = 'info') {
        const statusElement = document.getElementById('saveStatus');
        statusElement.textContent = message;
        
        autoSaveIndicator.className = `auto-save-indicator show ${type}`;
        
        setTimeout(() => {
            autoSaveIndicator.classList.remove('show');
        }, 2000);
    }
    
    function validateForm() {
        const requiredFields = form.querySelectorAll('[data-validation="required"]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    }
    
    function showValidationErrors() {
        const firstInvalidField = form.querySelector('.is-invalid');
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            firstInvalidField.focus();
        }
        
        showToast('error', 'Please fill in all required fields.');
    }
    
    // Global functions
    window.saveDraft = function() {
        autoSaveForm();
        showToast('info', 'Draft saved successfully!');
    };
    
    function showToast(type, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: type === 'error' ? 6000 : 3000
        });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
});
</script>
@endpush