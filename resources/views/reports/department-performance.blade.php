{{-- resources/views/reports/department-performance.blade.php --}}
@extends('layouts.app')

@section('title', 'Department Performance Report')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Department Performance</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Department Performance Report</h1>
            <p class="text-muted mb-0">Analyze department efficiency and completion rates</p>
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

@section('content')
    <!-- Report Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Report Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.department-performance') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">{{ number_format($overallStats['total_documents']) }}</h3>
                    <p class="text-muted mb-0">Total Documents</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-1">{{ number_format($overallStats['total_pending']) }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">{{ number_format($overallStats['total_completed']) }}</h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ number_format($overallStats['overall_completion_rate'], 1) }}%</h3>
                    <p class="text-muted mb-0">Avg Completion Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Performance Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Completion Rate by Department</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Performance Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Department Performance Details</h5>
        </div>
        <div class="card-body p-0">
            @if(count($performanceData) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th class="text-center">Total Documents</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Completed</th>
                                <th class="text-center">Overdue</th>
                                <th class="text-center">Completion Rate</th>
                                <th class="text-center">Avg Processing Time</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performanceData as $data)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $data['department']->name }}</h6>
                                            <small class="text-muted">{{ $data['department']->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ number_format($data['total_documents']) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-warning fw-bold">{{ number_format($data['pending_documents']) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-success fw-bold">{{ number_format($data['completed_documents']) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($data['overdue_documents'] > 0)
                                        <span class="text-danger fw-bold">{{ number_format($data['overdue_documents']) }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="me-2 fw-bold">{{ $data['completion_rate'] }}%</span>
                                        <div class="progress" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-{{ $data['completion_rate'] >= 80 ? 'success' : ($data['completion_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $data['completion_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $data['avg_processing_time'] }}h</span>
                                </td>
                                <td class="text-center">
                                    @if($data['completion_rate'] >= 80)
                                        <span class="badge bg-success">Excellent</span>
                                    @elseif($data['completion_rate'] >= 60)
                                        <span class="badge bg-warning">Good</span>
                                    @elseif($data['completion_rate'] >= 40)
                                        <span class="badge bg-info">Fair</span>
                                    @else
                                        <span class="badge bg-danger">Needs Improvement</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No performance data available</h5>
                    <p class="text-muted">Try adjusting your date filters to see performance data.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Top Performing Departments</h6>
                </div>
                <div class="card-body">
                    @php
                        $topPerformers = collect($performanceData)
                            ->sortByDesc('completion_rate')
                            ->take(5);
                    @endphp
                    
                    @foreach($topPerformers as $data)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ $data['department']->name }}</h6>
                            <small class="text-muted">{{ $data['total_documents'] }} documents</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-success">{{ $data['completion_rate'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Departments Needing Attention</h6>
                </div>
                <div class="card-body">
                    @php
                        $needsAttention = collect($performanceData)
                            ->where('overdue_documents', '>', 0)
                            ->sortByDesc('overdue_documents')
                            ->take(5);
                    @endphp
                    
                    @if($needsAttention->count() > 0)
                        @foreach($needsAttention as $data)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $data['department']->name }}</h6>
                                <small class="text-muted">{{ $data['completion_rate'] }}% completion rate</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-danger">{{ $data['overdue_documents'] }} overdue</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No overdue documents!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(performanceCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($performanceData as $data)
                    '{{ $data['department']->name }}',
                @endforeach
            ],
            datasets: [{
                label: 'Completion Rate (%)',
                data: [
                    @foreach($performanceData as $data)
                        {{ $data['completion_rate'] }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($performanceData as $data)
                        '{{ $data['completion_rate'] >= 80 ? '#10b981' : ($data['completion_rate'] >= 60 ? '#f59e0b' : '#ef4444') }}',
                    @endforeach
                ],
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '% completion rate';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
</script>
@endpush

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