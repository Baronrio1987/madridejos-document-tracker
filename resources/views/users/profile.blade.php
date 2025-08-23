@extends('layouts.app')

@section('title', 'My Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">My Profile</h1>
            <p class="text-muted mb-0">Manage your personal information and account settings</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <!-- Profile Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Change Password -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" id="passwordForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Current Password -->
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- New Password -->
                            <div class="col-md-4">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-key me-2"></i>Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Account Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Account Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted">Employee ID</label>
                            <p class="fw-semibold">{{ $user->employee_id }}</p>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label text-muted">Department</label>
                            <p class="fw-semibold">{{ $user->department->name ?? 'Not Assigned' }}</p>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label text-muted">Role</label>
                            <p>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'department_head' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </p>
                        </div>
                        
                        @if($user->position)
                        <div class="col-12">
                            <label class="form-label text-muted">Position</label>
                            <p class="fw-semibold">{{ $user->position }}</p>
                        </div>
                        @endif
                        
                        <div class="col-12">
                            <label class="form-label text-muted">Member Since</label>
                            <p class="fw-semibold">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                        
                        @if($user->last_login_at)
                        <div class="col-12">
                            <label class="form-label text-muted">Last Login</label>
                            <p class="fw-semibold">{{ $user->last_login_at->diffForHumans() }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Activity Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-activity me-2"></i>Activity Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $user->createdDocuments()->count() }}</div>
                                <small class="text-muted">Documents Created</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $user->routedDocuments()->count() }}</div>
                                <small class="text-muted">Documents Routed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $user->createdDocuments()->pending()->count() }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $user->createdDocuments()->completed()->count() }}</div>
                                <small class="text-muted">Completed</small>
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
    // Password form validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (!currentPassword && (newPassword || confirmPassword)) {
            e.preventDefault();
            alert('Please enter your current password to change it.');
            return false;
        }
        
        if (newPassword && newPassword !== confirmPassword) {
            e.preventDefault();
            alert('New password and confirmation do not match.');
            return false;
        }
        
        if (newPassword && newPassword.length < 8) {
            e.preventDefault();
            alert('New password must be at least 8 characters long.');
            return false;
        }
    });
    
    // Clear password fields if not changing password
    document.getElementById('profileForm').addEventListener('submit', function() {
        // Clear password fields in profile form
        document.getElementById('current_password').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
    });
</script>
@endpush