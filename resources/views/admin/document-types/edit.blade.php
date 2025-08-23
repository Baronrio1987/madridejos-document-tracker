@extends('layouts.app')

@section('title', 'Edit Document Type')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.document-types.index') }}">Document Types</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.document-types.show', $documentType) }}">{{ $documentType->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Edit Document Type</h1>
            <p class="text-muted mb-0">{{ $documentType->name }} ({{ $documentType->code }})</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.document-types.show', $documentType) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Document Type
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Type Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.document-types.update', $documentType) }}" id="documentTypeForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Document Type Name -->
                            <div class="col-md-8">
                                <label for="name" class="form-label">Document Type Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $documentType->name) }}" 
                                       placeholder="Enter document type name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Document Type Code -->
                            <div class="col-md-4">
                                <label for="code" class="form-label">Document Type Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $documentType->code) }}" 
                                       placeholder="e.g., MEMO" required maxlength="20" style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique identifier for the document type</small>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Enter document type description">{{ old('description', $documentType->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Retention Period -->
                            <div class="col-md-6">
                                <label for="retention_period" class="form-label">Retention Period (Days) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('retention_period') is-invalid @enderror" 
                                       id="retention_period" name="retention_period" 
                                       value="{{ old('retention_period', $documentType->retention_period) }}" 
                                       min="1" max="3650" required>
                                @error('retention_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How long documents of this type should be retained</small>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $documentType->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Document Type
                                    </label>
                                </div>
                                <small class="text-muted">Inactive document types cannot be used for new documents</small>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.document-types.show', $documentType) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update Document Type
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar with information -->
        <div class="col-xl-4">
            <!-- Current Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Current Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Created:</span>
                            <span>{{ $documentType->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Last Updated:</span>
                            <span>{{ $documentType->updated_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="text-muted">Documents:</span>
                            <span>{{ $documentType->documents()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Usage Warning -->
            @if($documentType->documents()->count() > 0)
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Usage Warning
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-0">
                        This document type is currently being used by {{ $documentType->documents()->count() }} document(s). 
                        Changes to the code or status may affect existing documents.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Uppercase code input
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Form validation
    document.getElementById('documentTypeForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const code = document.getElementById('code').value.trim();
        const retentionPeriod = parseInt(document.getElementById('retention_period').value);
        
        if (name.length < 3) {
            e.preventDefault();
            alert('Document type name must be at least 3 characters long.');
            return false;
        }
        
        if (code.length < 2) {
            e.preventDefault();
            alert('Document type code must be at least 2 characters long.');
            return false;
        }
        
        if (retentionPeriod < 1 || retentionPeriod > 3650) {
            e.preventDefault();
            alert('Retention period must be between 1 and 3650 days.');
            return false;
        }
    });
    
    // Track changes
    let formChanged = false;
    const formInputs = document.querySelectorAll('#documentTypeForm input, #documentTypeForm textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            formChanged = true;
        });
    });
    
    // Warn before leaving if form has changes
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Don't warn when submitting
    document.getElementById('documentTypeForm').addEventListener('submit', function() {
        formChanged = false;
    });
</script>
@endpush