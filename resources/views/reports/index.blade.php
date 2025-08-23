@extends('layouts.app')

@section('title', 'Reports')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Reports</h1>
            <p class="text-muted mb-0">Generate comprehensive reports and insights</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Document Summary Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-file-earmark-text" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Document Summary</h5>
                    <p class="card-text text-muted">Comprehensive overview of all documents with filtering options and export capabilities.</p>
                    <a href="{{ route('reports.document-summary') }}" class="btn btn-primary">
                        <i class="bi bi-graph-up me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Department Performance Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-building" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Department Performance</h5>
                    <p class="card-text text-muted">Analyze department efficiency, completion rates, and processing times.</p>
                    <a href="{{ route('reports.department-performance') }}" class="btn btn-success">
                        <i class="bi bi-bar-chart me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Monthly Trends Report -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="bi bi-graph-up-arrow" style="font-size: 1.5rem;"></i>
                    </div>
                    <h5 class="card-title">Monthly Trends</h5>
                    <p class="card-text text-muted">Track document creation and completion trends over time with detailed analytics.</p>
                    <a href="{{ route('reports.monthly-trends') }}" class="btn btn-info">
                        <i class="bi bi-calendar-week me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Report Generators -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Report Generators</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Overdue Documents -->
                        <div class="col-md-3">
                            <button class="btn btn-outline-warning w-100" onclick="generateQuickReport('overdue')">
                                <i class="bi bi-clock me-2"></i>
                                <div>Overdue Documents</div>
                                <small class="text-muted">Documents past deadline</small>
                            </button>
                        </div>

                        <!-- High Priority -->
                        <div class="col-md-3">
                            <button class="btn btn-outline-danger w-100" onclick="generateQuickReport('urgent')">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <div>Urgent Documents</div>
                                <small class="text-muted">High & urgent priority</small>
                            </button>
                        </div>

                        <!-- Completed Today -->
                        <div class="col-md-3">
                            <button class="btn btn-outline-success w-100" onclick="generateQuickReport('completed-today')">
                                <i class="bi bi-check-circle me-2"></i>
                                <div>Completed Today</div>
                                <small class="text-muted">Documents completed today</small>
                            </button>
                        </div>

                        <!-- Pending Routes -->
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100" onclick="generateQuickReport('pending-routes')">
                                <i class="bi bi-arrow-left-right me-2"></i>
                                <div>Pending Routes</div>
                                <small class="text-muted">Documents awaiting routing</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function generateQuickReport(type) {
        const today = new Date().toISOString().split('T')[0];
        let url = '';

        switch(type) {
            case 'overdue':
                url = `{{ route('reports.document-summary') }}?status=&date_to=${today}&format=pdf`;
                break;
            case 'urgent':
                url = `{{ route('reports.document-summary') }}?priority=urgent,high&format=pdf`;
                break;
            case 'completed-today':
                url = `{{ route('reports.document-summary') }}?status=completed&date_from=${today}&date_to=${today}&format=pdf`;
                break;
            case 'pending-routes':
                url = `{{ route('reports.document-summary') }}?status=in_progress&format=pdf`;
                break;
        }

        if (url) {
            window.open(url, '_blank');
        }
    }
</script>
@endpush