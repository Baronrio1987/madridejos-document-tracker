@extends('layouts.app')

@section('title', 'Edit Department')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.departments.show', $department) }}">{{ $department->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Edit Department</h1>
            <p class="text-muted mb-0">{{ $department->name }} ({{ $department->code }})</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Department
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Department Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Department Name -->
                            <div class="col-md-8">
                                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $department->name) }}" 
                                       placeholder="Enter department name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Department Code -->
                            <div class="col-md-4">
                                <label for="code" class="form-label">Department Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $department->code) }}" 
                                       placeholder="e.g., MAYOR" required maxlength="20" style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Enter department description">{{ old('description', $department->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Department Head -->
                            <div class="col-md-6">
                                <label for="head_name" class="form-label">Department Head</label>
                                <input type="text" class="form-control @error('head_name') is-invalid @enderror" 
                                       id="head_name" name="head_name" value="{{ old('head_name', $department->head_name) }}" 
                                       placeholder="Enter department head name">
                                @error('head_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Department
                                    </label>
                                </div>
                                <small class="text-muted">Inactive departments cannot be assigned to users or documents</small>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update Department
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
            <!-- Current Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Current Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Created:</span>
                            <span class="fw-semibold">{{ $department->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Updated:</span>
                            <span class="fw-semibold">{{ $department->updated_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Users:</span>
                            <span class="fw-semibold">{{ $department->users()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="text-muted">Documents:</span>
                            <span class="fw-semibold">{{ $department->currentDocuments()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Warning -->
            @if($department->users()->exists() || $department->currentDocuments()->exists())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Note:</strong> This department has {{ $department->users()->count() }} users and {{ $department->currentDocuments()->count() }} current documents. Deactivating will affect these records.
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
</script>
@endpush
