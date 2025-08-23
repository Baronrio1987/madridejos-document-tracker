@extends('layouts.app')

@section('title', 'Department Details - ' . $department->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('analytics.index') }}">Analytics</a></li>
    <li class="breadcrumb-item"><a href="{{ route('analytics.department-performance') }}">Department Performance</a></li>
    <li class="breadcrumb-item active">{{ $department->name }}</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ $department->name }} - Detailed Analytics</h1>
            <p class="text-muted mb-0">Comprehensive performance metrics and document statistics</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('analytics.department-report', $department->id) }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Download Report
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-file-earmark-text" style="font-size: 1.5rem;"></i>
                    </div>
                    <h4 class="text-primary mb-1">{{ number_format($stats['total_documents']) }}</h4>
                    <small class="text-muted">Total Documents</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                    </div>
                    <h4 class="text-success mb-1">{{ number_format($stats['completed_documents']) }}</h4>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-clock" style="font-size: 1.5rem;"></i>
                    </div>
                    <h4 class="text-warning mb-1">{{ number_format($stats['pending_documents']) }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-speedometer2" style="font-size: 1.5rem;"></i>
                    </div>
                    <h4 class="text-info mb-1">{{ number_format($stats['avg_processing_time'], 1) }}h</h4>
                    <small class="text-muted">Avg Processing Time</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Completion Rate Progress</h5>
                </div>
                <div class="card-body">
                    @php
                        $completionRate = $stats['total_documents'] > 0 ? 
                            round(($stats['completed_documents'] / $stats['total_documents']) * 100, 2) : 0;
                    @endphp
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Overall Completion Rate</span>
                        <span class="fw-bold">{{ $completionRate }}%</span>
                    </div>
                    <div class="progress mb-4" style="height: 12px;">
                        <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                    </div>
                    
                    <div class="row g-3 text-center">
                        <div class="col">
                            <div class="border rounded p-2">
                                <div class="h6 mb-0 text-success">{{ $stats['completed_documents'] }}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2">
                                <div class="h6 mb-0 text-primary">{{ $stats['in_progress_documents'] }}</div>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2">
                                <div class="h6 mb-0 text-warning">{{ $stats['pending_documents'] }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2">
                                <div class="h6 mb-0 text-danger">{{ $stats['overdue_documents'] }}</div>
                                <small class="text-muted">Overdue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Department Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted">Department Code</label>
                        <div class="fw-semibold">{{ $department->code }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Department Head</label>
                        <div class="fw-semibold">{{ $department->head_name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Active Users</label>
                        <div class="fw-semibold">{{ $department->users()->where('is_active', true)->count() }}</div>
                    </div>
                    <div>
                        <label class="small text-muted">Status</label>
                        <div>
                            <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Documents -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Documents</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created By</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_documents'] as $doc)
                        <tr>
                            <td class="fw-semibold">{{ $doc->tracking_number }}</td>
                            <td>{{ Str::limit($doc->title, 40) }}</td>
                            <td>{{ $doc->documentType->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $doc->status === 'completed' ? 'success' : ($doc->status === 'pending' ? 'warning' : 'primary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $doc->priority === 'urgent' ? 'danger' : ($doc->priority === 'high' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($doc->priority) }}
                                </span>
                            </td>
                            <td>{{ $doc->creator->name ?? 'N/A' }}</td>
                            <td class="text-muted">{{ $doc->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No documents found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection