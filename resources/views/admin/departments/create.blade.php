@extends('layouts.app')

@section('title', 'Create Department')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
    <li class="breadcrumb-item active">Create Department</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Create New Department</h1>
            <p class="text-muted mb-0">Add a new department to the organization</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Departments
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
                    <form method="POST" action="{{ route('admin.departments.store') }}" id="departmentForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Department Name -->
                            <div class="col-md-8">
                                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Enter department name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Department Code -->
                            <div class="col-md-4">
                                <label for="code" class="form-label">Department Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="e.g., MAYOR" required maxlength="20" style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique identifier for the department</small>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Enter department description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Department Head -->
                            <div class="col-md-6">
                                <label for="head_name" class="form-label">Department Head</label>
                                <input type="text" class="form-control @error('head_name') is-invalid @enderror" 
                                       id="head_name" name="head_name" value="{{ old('head_name') }}" 
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
                                           {{ old('is_active', true) ? 'checked' : '' }}>
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
                                    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create Department
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar with guidelines -->
        <div class="col-xl-4">
            <!-- Guidelines -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use clear, descriptive names
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Keep codes short and memorable
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Include official department head
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Add detailed description for clarity
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Existing Departments -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Existing Departments</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        @forelse(\App\Models\Department::active()->limit(5)->get() as $dept)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $dept->name }}</span>
                            <span class="badge bg-light text-dark">{{ $dept->code }}</span>
                        </div>
                        @empty
                        <p class="text-muted mb-0">No departments yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-generate code from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const codeField = document.getElementById('code');
        
        if (!codeField.value || codeField.dataset.autoGenerated) {
            // Generate code from name (first letters of words)
            const words = name.split(' ');
            let code = '';
            words.forEach(word => {
                if (word.length > 0) {
                    code += word.charAt(0).toUpperCase();
                }
            });
            
            // Limit to 20 characters
            code = code.substring(0, 20);
            codeField.value = code;
            codeField.dataset.autoGenerated = 'true';
        }
    });
    
    // Remove auto-generated flag when user manually edits code
    document.getElementById('code').addEventListener('input', function() {
        delete this.dataset.autoGenerated;
    });
    
    // Uppercase code input
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Form validation
    document.getElementById('departmentForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const code = document.getElementById('code').value.trim();
        
        if (name.length < 3) {
            e.preventDefault();
            alert('Department name must be at least 3 characters long.');
            return false;
        }
        
        if (code.length < 2) {
            e.preventDefault();
            alert('Department code must be at least 2 characters long.');
            return false;
        }
    });
</script>
@endpush