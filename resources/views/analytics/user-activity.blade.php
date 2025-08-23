@extends('layouts.app')

@section('title', 'User Activity Analytics')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('analytics.index') }}">Analytics</a></li>
    <li class="breadcrumb-item active">User Activity</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">User Activity Analytics</h1>
            <p class="text-muted mb-0">Monitor user engagement and system usage patterns</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Top Users -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Most Active Users</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Documents Created</th>
                                    <th>Documents Routed</th>
                                    <th>Documents Received</th>
                                    <th>Total Activity</th>
                                    <th>Last Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userStats as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 36px; height: 36px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->department->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format($user->created_documents_count) }}</td>
                                    <td class="text-info">{{ number_format($user->routed_documents_count) }}</td>
                                    <td class="text-success">{{ number_format($user->received_documents_count) }}</td>
                                    <td class="fw-semibold text-primary">
                                        {{ number_format($user->created_documents_count + $user->routed_documents_count + $user->received_documents_count) }}
                                    </td>
                                    <td class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <!-- Activity Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Activity Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-primary">{{ number_format($userStats->sum('created_documents_count')) }}</div>
                                <small class="text-muted">Documents Created</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-info">{{ number_format($userStats->sum('routed_documents_count')) }}</div>
                                <small class="text-muted">Documents Routed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-success">{{ number_format($userStats->sum('received_documents_count')) }}</div>
                                <small class="text-muted">Documents Received</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-warning">{{ $userStats->where('is_active', true)->count() }}</div>
                                <small class="text-muted">Active Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Roles Distribution -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">User Roles</h6>
                </div>
                <div class="card-body">
                    <canvas id="rolesChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent System Activities</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivities as $activity)
                        <tr>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->format('M d, H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 24px; height: 24px;">
                                        <i class="bi bi-person small"></i>
                                    </div>
                                    {{ $activity->user_name }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</span>
                            </td>
                            <td>{{ $activity->description }}</td>
                            <td class="text-muted small">{{ $activity->ip_address }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // User Roles Chart
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    const rolesChart = new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Admin', 'Department Head', 'Encoder', 'Viewer'],
            datasets: [{
                data: [
                    {{ $userStats->where('role', 'admin')->count() }},
                    {{ $userStats->where('role', 'department_head')->count() }},
                    {{ $userStats->where('role', 'encoder')->count() }},
                    {{ $userStats->where('role', 'viewer')->count() }}
                ],
                backgroundColor: [
                    '#dc2626',
                    '#f59e0b',
                    '#3b82f6',
                    '#6b7280'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush