@extends('layouts.app')

@section('title', $department->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
    <li class="breadcrumb-item active">{{ $department->name }}</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ $department->name }}</h1>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary me-2">{{ $department->code }}</span>
                {{ $department->is_active ? 'Active Department' : 'Inactive Department' }}
            </p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Cards for Total Users, Active Users, etc. -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h4 fw-bold mb-0">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- More cards here... -->
    </div>

    <!-- Department Information Section -->
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Department Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Department Name</label>
                            <p class="fw-semibold">{{ $department->name }}</p>
                        </div>

                        <!-- More fields here... -->

                    </div>
                </div>
            </div>
        </div>

        <!-- Department Users Section -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Department Users</h6>
                    <span class="badge bg-primary">{{ $department->users->count() }}</span>
                </div>
                <div class="card-body">
                    @if($department->users->count() > 0)
                        @foreach($department->users->take(10) as $user)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 32px; height: 32px;">
                                <i class="bi bi-person small"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small">{{ $user->name }}</h6>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</small>
                            </div>
                            @if(!$user->is_active)
                                <span class="badge bg-warning">Inactive</span>
                            @endif
                        </div>
                        @endforeach

                        <!-- View All Users Button -->
                        @if($department->users->count() > 10)
                        <small class="text-muted">...and {{ $department->users->count() - 10 }} more users</small>
                        @endif
                        
                        <div class="d-grid mt-3">
                            <a href="{{ route('users.index', ['department_id' => $department->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Users
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-people" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2 small">No users assigned</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
