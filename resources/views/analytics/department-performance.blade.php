@extends('layouts.app')

@section('title', 'Department Performance Analytics')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('analytics.index') }}">Analytics</a></li>
    <li class="breadcrumb-item active">Department Performance</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Department Performance Analytics</h1>
            <p class="text-muted mb-0">Detailed analysis of department efficiency and metrics</p>
        </div>
        <div class="col-auto">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportData('csv')">Export CSV</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportData('excel')">Export Excel</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Performance Metrics -->
    <div class="row g-4 mb-4">
        @foreach($performanceData as $data)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-building" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="viewDetails('{{ $data['department']->id }}')">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="generateReport('{{ $data['department']->id }}')">
                                    <i class="bi bi-file-text me-2"></i>Generate Report
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h5 class="card-title mb-2">{{ $data['department']->name }}</h5>
                    <p class="text-muted small mb-3">{{ $data['department']->code }}</p>
                    
                    <!-- Key Metrics -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="h5 mb-0 text-primary">{{ number_format($data['total_documents']) }}</div>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="h5 mb-0 text-success">{{ $data['completion_rate'] }}%</div>
                                <small class="text-muted">Complete</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completion Rate Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Completion Rate</small>
                            <small class="fw-semibold">{{ $data['completion_rate'] }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $data['completion_rate'] }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Processing Time -->
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Avg Processing Time</small>
                        <span class="badge bg-info">{{ $data['average_processing_time'] }}h</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Comparison Chart -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Department Comparison</h5>
        </div>
        <div class="card-body">
            <canvas id="comparisonChart" height="100"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Department Comparison Chart
    const ctx = document.getElementById('comparisonChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(collect($performanceData)->pluck('department.name')),
            datasets: [
                {
                    label: 'Total Documents',
                    data: @json(collect($performanceData)->pluck('total_documents')),
                    backgroundColor: 'rgba(30, 64, 175, 0.8)',
                    borderColor: '#1e40af',
                    borderWidth: 1
                },
                {
                    label: 'Completed Documents',
                    data: @json(collect($performanceData)->pluck('completed_documents')),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    function exportData(format) {
        const button = event.target.closest('a');
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Exporting...';
        
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("analytics.export") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="type" value="departments">
            <input type="hidden" name="format" value="${format === 'csv' ? 'csv' : 'xlsx'}">
        `;
        
        document.body.appendChild(form);
        form.submit();
        
        // Reset button after delay
        setTimeout(() => {
            button.innerHTML = originalText;
            if (document.body.contains(form)) {
                document.body.removeChild(form);
            }
        }, 3000);
    }

    function viewDetails(departmentId) {
        // Use Laravel route helper instead of template literal
        const url = '{{ route("analytics.department-details", ":id") }}'.replace(':id', departmentId);
        window.location.href = url;
    }

    function generateReport(departmentId) {
        const button = event.target.closest('a');
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating...';
        
        // Use Laravel route helper instead of template literal
        const url = '{{ route("analytics.department-report", ":id") }}'.replace(':id', departmentId);
        window.location.href = url;
        
        // Reset button after delay
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 3000);
    }
</script>
@endpush