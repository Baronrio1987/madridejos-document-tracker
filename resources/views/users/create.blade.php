@extends('layouts.app')

@section('title', 'Create User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">Create User</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Create New User</h1>
            <p class="text-muted mb-0">Add a new user to the system</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Users
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
                    <form method="POST" action="{{ route('users.store') }}" id="userForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Employee ID -->
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id') }}" 
                                       placeholder="e.g., EMP-001" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique identifier for the employee</small>
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="Enter phone number">
                                @error('phone')
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
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                                    <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
                                    <option value="encoder" {{ old('role') == 'encoder' ? 'selected' : '' }}>Encoder</option>
                                    <option value="department_head" {{ old('role') == 'department_head' ? 'selected' : '' }}>Department Head</option>
                                    @if(Auth::user()->isAdmin())
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    @endif
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Position -->
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position') }}" 
                                       placeholder="Enter job position">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Enter password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Confirm password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Active Status -->
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Account is Active
                                    </label>
                                </div>
                                <small class="text-muted">Inactive users cannot log in to the system</small>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create User
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
            <!-- Role Descriptions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>User Roles</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <div class="fw-semibold text-danger">Admin</div>
                            <div class="text-muted">Full system access and user management</div>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold text-warning">Department Head</div>
                            <div class="text-muted">Department management and reports access</div>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold text-info">Encoder</div>
                            <div class="text-muted">Can create and route documents</div>
                        </div>
                        <div class="mb-0">
                            <div class="fw-semibold text-secondary">Viewer</div>
                            <div class="text-muted">Read-only access to documents</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Guidelines -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use official employee email addresses
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Follow employee ID naming convention
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Assign appropriate role for job function
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Set strong default password
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Password Requirements -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Password Requirements</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-1">
                            <i class="bi bi-check me-2"></i>
                            At least 8 characters long
                        </li>
                        <li class="mb-1">
                            <i class="bi bi-check me-2"></i>
                            Mix of letters and numbers
                        </li>
                        <li class="mb-1">
                            <i class="bi bi-check me-2"></i>
                            Include special characters
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check me-2"></i>
                            User will be required to change on first login
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
        function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Full Name validation - only letters, spaces, periods, and apostrophes
    document.getElementById('name').addEventListener('input', function(e) {
        // Remove any character that's not a letter, space, period, or apostrophe
        let value = this.value;
        let cleaned = value.replace(/[^a-zA-Z\s.']/g, '');
        
        // Prevent multiple consecutive spaces
        cleaned = cleaned.replace(/\s{2,}/g, ' ');
        
        if (value !== cleaned) {
            this.value = cleaned;
            // Show brief feedback
            this.classList.add('border-warning');
            setTimeout(() => {
                this.classList.remove('border-warning');
            }, 500);
        }
    });

    // Phone number validation - only numbers, exactly 11 digits
    document.getElementById('phone').addEventListener('input', function(e) {
        // Remove any non-numeric characters
        let value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 11 digits
        if (value.length > 11) {
            value = value.slice(0, 11);
        }
        
        this.value = value;
        
        // Visual feedback for length
        if (value.length > 0 && value.length < 11) {
            this.classList.add('border-warning');
            this.classList.remove('border-success');
        } else if (value.length === 11) {
            this.classList.remove('border-warning');
            this.classList.add('border-success');
            setTimeout(() => {
                this.classList.remove('border-success');
            }, 1000);
        } else {
            this.classList.remove('border-warning', 'border-success');
        }
    });

    // Phone number validation on blur - set to N/A if empty
    document.getElementById('phone').addEventListener('blur', function(e) {
        if (this.value.trim() === '') {
            this.value = 'N/A';
        } else if (this.value !== 'N/A' && this.value.length !== 11) {
            // Show error if not exactly 11 digits
            this.classList.add('is-invalid');
            
            // Add or update error message
            let feedback = this.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                this.parentNode.appendChild(feedback);
            }
            feedback.textContent = 'Phone number must be exactly 11 digits';
        }
    });

    // Phone number validation on focus - clear N/A
    document.getElementById('phone').addEventListener('focus', function(e) {
        if (this.value === 'N/A') {
            this.value = '';
        }
        this.classList.remove('is-invalid');
    });

    // Auto-generate employee ID
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const empIdField = document.getElementById('employee_id');
        
        if (!empIdField.value || empIdField.dataset.autoGenerated) {
            // Generate employee ID from name (first 3 letters + numbers)
            const nameClean = name.replace(/[^a-zA-Z]/g, '').toUpperCase();
            const namePrefix = nameClean.substring(0, 3);
            
            if (namePrefix.length >= 2) {
                // Get current count for next number
                const randomNum = Math.floor(Math.random() * 999) + 1;
                const empId = `EMP-${String(randomNum).padStart(3, '0')}`;
                empIdField.value = empId;
                empIdField.dataset.autoGenerated = 'true';
            }
        }
    });

    // Remove auto-generated flag when user manually edits
    document.getElementById('employee_id').addEventListener('input', function() {
        delete this.dataset.autoGenerated;
    });

    // Auto-suggest position based on role
    document.getElementById('role').addEventListener('change', function() {
        const positionField = document.getElementById('position');
        const role = this.value;
        
        if (!positionField.value || positionField.dataset.autoSuggested) {
            let suggestion = '';
            switch(role) {
                case 'admin':
                    suggestion = 'System Administrator';
                    break;
                case 'department_head':
                    suggestion = 'Department Head';
                    break;
                case 'encoder':
                    suggestion = 'Document Encoder';
                    break;
                case 'viewer':
                    suggestion = 'Document Viewer';
                    break;
            }
            
            if (suggestion) {
                positionField.value = suggestion;
                positionField.dataset.autoSuggested = 'true';
            }
        }
    });

    // Remove auto-suggested flag when user manually edits position
    document.getElementById('position').addEventListener('input', function() {
        delete this.dataset.autoSuggested;
    });

    // Form validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        // Validate Full Name
        const name = document.getElementById('name').value.trim();
        if (!/^[a-zA-Z\s.']+$/.test(name)) {
            isValid = false;
            errorMessages.push('Full name can only contain letters, spaces, periods, and apostrophes.');
        }
        
        // Validate Phone Number
        const phone = document.getElementById('phone').value.trim();
        if (phone !== 'N/A') {
            if (!/^\d{11}$/.test(phone)) {
                isValid = false;
                errorMessages.push('Phone number must be exactly 11 digits or leave blank for N/A.');
            }
        }
        
        // Validate Password
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
            isValid = false;
            errorMessages.push('Password and confirmation do not match.');
        }
        
        if (password.length < 8) {
            isValid = false;
            errorMessages.push('Password must be at least 8 characters long.');
        }
        
        // Show errors if validation fails
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
            return false;
        }
    });

    // Generate random password button
    function generatePassword() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        
        document.getElementById('password').value = password;
        document.getElementById('password_confirmation').value = password;
        
        // Show password temporarily
        document.getElementById('password').type = 'text';
        document.getElementById('password_confirmation').type = 'text';
        
        setTimeout(() => {
            document.getElementById('password').type = 'password';
            document.getElementById('password_confirmation').type = 'password';
        }, 3000);
    }

    // Add generate password button
    document.addEventListener('DOMContentLoaded', function() {
        const passwordGroup = document.querySelector('#password').closest('.input-group');
        const generateBtn = document.createElement('button');
        generateBtn.type = 'button';
        generateBtn.className = 'btn btn-outline-info';
        generateBtn.innerHTML = '<i class="bi bi-shuffle"></i>';
        generateBtn.title = 'Generate random password';
        generateBtn.onclick = generatePassword;
        
        passwordGroup.appendChild(generateBtn);
        
        // Add helper text for phone number
        const phoneField = document.getElementById('phone');
        const phoneHelp = document.createElement('small');
        phoneHelp.className = 'text-muted';
        phoneHelp.id = 'phoneHelp';
        phoneHelp.textContent = 'Must be exactly 11 digits. Leave blank for N/A';
        
        // Insert after phone field
        if (phoneField.parentNode.querySelector('.invalid-feedback')) {
            phoneField.parentNode.querySelector('.invalid-feedback').after(phoneHelp);
        } else {
            phoneField.parentNode.appendChild(phoneHelp);
        }
        
        // Add helper text for name field
        const nameField = document.getElementById('name');
        const nameHelp = document.createElement('small');
        nameHelp.className = 'text-muted';
        nameHelp.textContent = 'Only letters, spaces, periods (.), and apostrophes (\') allowed';
        nameField.parentNode.appendChild(nameHelp);
    });
</script>
@endpush