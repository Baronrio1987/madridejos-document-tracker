@extends('layouts.app')

@section('title', 'Users')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">User Management</h1>
            <p class="text-muted mb-0">Manage system users and their roles</p>
        </div>
        <div class="col-auto">
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add User
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Name, email, employee ID...">
                </div>

                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="department_head" {{ request('role') == 'department_head' ? 'selected' : '' }}>
                            Department Head</option>
                        <option value="encoder" {{ request('role') == 'encoder' ? 'selected' : '' }}>Encoder</option>
                        <option value="viewer" {{ request('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if ($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Employee ID</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ $user->employee_id }}</td>
                                    <td class="text-muted">{{ $user->department->name ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'department_head' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                id="status_{{ $user->id }}"
                                                {{ $user->is_active ? 'checked' : '' }}
                                                onchange="toggleUserStatus({{ $user->id }})"
                                                {{ Auth::id() === $user->id ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="status_{{ $user->id }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @can('view', $user)
                                                    <li><a class="dropdown-item" href="{{ route('users.show', $user) }}">
                                                            <i class="bi bi-eye me-2"></i>View Profile
                                                        </a></li>
                                                @endcan
                                                @can('update', $user)
                                                    <li><a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </a></li>
                                                @endcan
                                                @if (Auth::id() !== $user->id)
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><button class="dropdown-item"
                                                            onclick="resetPassword('{{ $user->id }}')">
                                                            <i class="bi bi-key me-2"></i>Reset Password
                                                        </button></li>
                                                    @can('delete', $user)
                                                        <li><button class="dropdown-item text-danger"
                                                                onclick="deleteUser('{{ $user->id }}')">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </button></li>
                                                    @endcan
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-0">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No users found</h5>
                    <p class="text-muted">
                        @if (request()->hasAny(['search', 'department_id', 'role', 'status']))
                            Try adjusting your filters or <a href="{{ route('users.index') }}">clear all filters</a>.
                        @else
                            Create your first user to get started.
                        @endif
                    </p>
                    @can('create', App\Models\User::class)
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add User
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Include jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Global CSRF setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toggleUserStatus(userId) {
            // Get the checkbox that was clicked
            const checkbox = event.target;
            const originalState = !checkbox.checked;
            
            // Make AJAX request to toggle status
            $.ajax({
                url: `/api/users/${userId}/toggle-status`, // Use API route
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        // Update the label text
                        const label = checkbox.closest('.form-check').querySelector('.form-check-label');
                        label.textContent = response.is_active ? 'Active' : 'Inactive';
                        
                        // Show success message
                        showAlert('success', response.message);
                    } else {
                        // Revert checkbox state
                        checkbox.checked = originalState;
                        showAlert('error', response.message || 'Failed to update user status');
                    }
                },
                error: function(xhr) {
                    // Revert checkbox state
                    checkbox.checked = originalState;
                    
                    let errorMessage = 'An error occurred while updating user status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        errorMessage = 'You do not have permission to perform this action.';
                    } else if (xhr.status === 401) {
                        errorMessage = 'You are not authenticated. Please refresh the page and try again.';
                    }
                    
                    showAlert('error', errorMessage);
                    console.error('Error:', xhr);
                }
            });
        }

        function resetPassword(userId) {
            if (confirm('Are you sure you want to reset this user\'s password? A new temporary password will be generated.')) {
                showAlert('info', 'Password reset functionality would be implemented here');
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/users/${userId}`;
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = $('meta[name="csrf-token"]').attr('content');
                
                // Add method override for DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Helper function to show alerts
        function showAlert(type, message) {
            // Remove existing alerts
            $('.alert-dynamic').remove();
            
            // Create new alert
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 
                              type === 'warning' ? 'alert-warning' : 'alert-info';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show alert-dynamic" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 
                                      type === 'error' ? 'exclamation-triangle' : 
                                      type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Insert at the top of the main content
            $('.main-content .container-fluid').prepend(alertHtml);
            
            // Auto-hide success and info alerts after 5 seconds
            if (type === 'success' || type === 'info') {
                setTimeout(() => {
                    $('.alert-dynamic').fadeOut();
                }, 5000);
            }
        }

        // Document ready initialization
        $(document).ready(function() {
            // Initialize tooltips if needed
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush