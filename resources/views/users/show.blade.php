@extends('layouts.app')

@section('title', $user->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ $user->name }}</h1>
            <p class="text-muted mb-0">{{ $user->email }} • {{ $user->department->name ?? 'No Department' }}</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                @can('update', $user)
                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </a>
                @endcan
                
                @if(Auth::id() !== $user->id)
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item" onclick="toggleUserStatus('{{ $user->id }}')">
                            <i class="bi bi-toggle-{{ $user->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button></li>
                        <li><button class="dropdown-item" onclick="resetPassword('{{ $user->id }}')">
                            <i class="bi bi-key me-2"></i>Reset Password
                        </button></li>
                        @can('delete', $user)
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" onclick="deleteUser('{{ $user->id }}')">
                            <i class="bi bi-trash me-2"></i>Delete User
                        </button></li>
                        @endcan
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <!-- User Information -->
        <div class="col-xl-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Full Name</label>
                            <p class="fw-semibold">{{ $user->name }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email Address</label>
                            <p class="fw-semibold">{{ $user->email }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Employee ID</label>
                            <p class="fw-semibold">{{ $user->employee_id }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Department</label>
                            <p class="fw-semibold">{{ $user->department->name ?? 'Not Assigned' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Role</label>
                            <p>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'department_head' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <p>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        
                        @if($user->position)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Position</label>
                            <p class="fw-semibold">{{ $user->position }}</p>
                        </div>
                        @endif
                        
                        @if($user->phone)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone Number</label>
                            <p class="fw-semibold">{{ $user->phone }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Member Since</label>
                            <p class="fw-semibold">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                        
                        @if($user->last_login_at)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Login</label>
                            <p class="fw-semibold">{{ $user->last_login_at->diffForHumans() }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Recent Documents -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Documents</h5>
                    <a href="{{ route('documents.index', ['search' => $user->name]) }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($user->createdDocuments()->latest()->limit(5)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tracking #</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->createdDocuments()->latest()->limit(5)->get() as $document)
                                    <tr onclick="window.location=&quot;{{ route('documents.show', $document) }}&quot;;" style="cursor: pointer;">
                                        <td class="fw-semibold">{{ $document->tracking_number }}</td>
                                        <td>{{ Str::limit($document->title, 40) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $document->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $document->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No documents created yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Activity Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 text-primary mb-1">{{ number_format($stats['documents_created']) }}</div>
                                <small class="text-muted">Documents Created</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 text-info mb-1">{{ number_format($stats['documents_routed']) }}</div>
                                <small class="text-muted">Documents Routed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 text-warning mb-1">{{ number_format($stats['pending_documents']) }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 text-success mb-1">{{ number_format($stats['completed_documents']) }}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Account Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted">Account Status</span>
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted">Email Verified</span>
                        <span class="badge bg-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </span>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted">Role Level</span>
                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'department_head' ? 'warning' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>
                    
                    @if($user->last_login_at)
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">Last Active</span>
                        <span class="fw-semibold">{{ $user->last_login_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                        @endcan
                        
                        @if(Auth::id() !== $user->id)
                        <button class="btn btn-outline-secondary" onclick="resetPassword('{{ $user->id }}')">
                            <i class="bi bi-key me-2"></i>Reset Password
                        </button>
                        
                        <button class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" onclick="toggleUserStatus('{{ $user->id }}')">
                            <i class="bi bi-toggle-{{ $user->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        @endif
                        
                        <a href="{{ route('documents.index', ['search' => $user->name]) }}" class="btn btn-outline-info">
                            <i class="bi bi-file-earmark-text me-2"></i>View Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleUserStatus(userId) {
        if (confirm('Are you sure you want to change the status of this user?')) {
            fetch(`/api/users/${userId}/toggle-status`, {
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
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating user status.');
            });
        }
    }
    
    function resetPassword(userId) {
        if (confirm('Are you sure you want to reset this user\'s password? A new temporary password will be generated.')) {
            // This would typically send a password reset email
            alert('Password reset functionality would be implemented here');
        }
    }
    
    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/users/${userId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush