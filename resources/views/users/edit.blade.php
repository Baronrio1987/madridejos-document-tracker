@extends('layouts.app')

@section('title', 'Edit User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Edit User</h1>
            <p class="text-muted mb-0">{{ $user->name }} - {{ $user->email }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to User
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}" id="userForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" 
                                       placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Employee ID -->
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" 
                                       placeholder="e.g., EMP-001" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Department -->
                            <div class="col-md-6">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="department_head" {{ old('role', $user->role) == 'department_head' ? 'selected' : '' }}>Department Head</option>
                                    <option value="encoder" {{ old('role', $user->role) == 'encoder' ? 'selected' : '' }}>Encoder</option>
                                    <option value="viewer" {{ old('role', $user->role) == 'viewer' ? 'selected' : '' }}>Viewer</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Position -->
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $user->position) }}" 
                                       placeholder="Enter position title">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password (Optional) -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" minlength="8">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave blank to keep current password</small>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" minlength="8">
                            </div>
                            
                            <!-- Active Status -->
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active User Account
                                    </label>
                                </div>
                                <small class="text-muted">Inactive users cannot log in to the system</small>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update User
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
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Current Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Created:</span>
                            <span class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Last Updated:</span>
                            <span class="fw-semibold">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                        @if($user->last_login_at)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Last Login:</span>
                            <span class="fw-semibold">{{ $user->last_login_at->diffForHumans() }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Documents:</span>
                            <span class="fw-semibold">{{ $user->createdDocuments()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Role Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Role Permissions</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <span class="badge bg-danger me-2">Admin</span>
                            <span>Full system access</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-warning me-2">Department Head</span>
                            <span>Department + reports</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-secondary me-2">Encoder</span>
                            <span>Create & route documents</span>
                        </div>
                        <div class="mb-0">
                            <span class="badge bg-light text-dark me-2">Viewer</span>
                            <span>Read-only access</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password && password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Form validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Password and confirmation password do not match.');
            return false;
        }
        
        if (password && password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return false;
        }
    });
    
    // Track changes
    let formChanged = false;
    const formInputs = document.querySelectorAll('#userForm input, #userForm select');
    
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
    document.getElementById('userForm').addEventListener('submit', function() {
        formChanged = false;
    });
</script>
@endpush