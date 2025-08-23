@extends('layouts.app')

@section('title', 'Document Summary Report')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Document Summary</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Document Summary Report</h1>
            <p class="text-muted mb-0">Comprehensive overview of document activities</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
                <a href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Download PDF
                </a>
            </div>
        </div>
    </div>
@endsection

@section("content")
    <!-- Report Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Report Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.document-summary') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">{{ number_format($stats['total']) }}</h3>
                    <p class="text-muted mb-0">Total Documents</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-1">{{ number_format($stats['pending']) }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ number_format($stats['in_progress']) }}</h3>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">{{ number_format($stats['completed']) }}</h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Document Details</h5>
        </div>
        <div class="card-body p-0">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tracking #</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created Date</th>
                                <th>Completion Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                <td class="fw-semibold">{{ $document->tracking_number }}</td>
                                <td>{{ Str::limit($document->title, 40) }}</td>
                                <td class="text-muted">{{ $document->documentType->name }}</td>
                                <td class="text-muted">{{ $document->currentDepartment->name }}</td>
                                <td>
                                    <span class="status-badge status-{{ $document->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="bi bi-flag priority-{{ $document->priority }}"></i>
                                    {{ ucfirst($document->priority) }}
                                </td>
                                <td class="text-muted">{{ $document->created_at->format('M d, Y') }}</td>
                                <td class="text-muted">
                                    {{ $document->actual_completion_date ? $document->actual_completion_date->format('M d, Y') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No documents found</h5>
                    <p class="text-muted">Try adjusting your filters to see more results.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header, .breadcrumb {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .page-header {
            margin-bottom: 1rem !important;
        }
    }
</style>
@endpush