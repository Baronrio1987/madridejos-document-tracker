@extends('layouts.app')

@section('title', 'Analytics')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Analytics</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Analytics Dashboard</h1>
            <p class="text-muted mb-0">Comprehensive insights and performance metrics</p>
        </div>
        <div class="col-auto">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i>Export Data
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportData('documents', 'csv')">Documents (CSV)</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportData('routes', 'csv')">Routes (CSV)</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportData('users', 'xlsx')">Users (Excel)</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-primary text-uppercase mb-1">Total Documents</div>
                            <div class="h4 fw-bold mb-0" id="totalDocuments">{{ number_format($stats['total_documents']) }}</div>
                            <div class="text-xs text-muted">
                                <span class="text-success me-1"><i class="bi bi-arrow-up"></i></span>
                                <span id="documentsGrowth">12%</span> vs last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-success text-uppercase mb-1">Completion Rate</div>
                            <div class="h4 fw-bold mb-0" id="completionRate">
                                {{ $stats['total_documents'] > 0 ? number_format(($stats['completed_documents'] / $stats['total_documents']) * 100, 1) : 0 }}%
                            </div>
                            <div class="text-xs text-muted">
                                <span class="text-success me-1"><i class="bi bi-arrow-up"></i></span>
                                <span>2.1%</span> vs last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-warning text-uppercase mb-1">Avg Processing Time</div>
                            <div class="h4 fw-bold mb-0" id="avgProcessingTime">3.2 days</div>
                            <div class="text-xs text-muted">
                                <span class="text-danger me-1"><i class="bi bi-arrow-down"></i></span>
                                <span>0.5</span> days faster
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-info text-uppercase mb-1">Active Users</div>
                            <div class="h4 fw-bold mb-0" id="activeUsers">{{ number_format($stats['total_users']) }}</div>
                            <div class="text-xs text-muted">
                                <span class="text-success me-1"><i class="bi bi-arrow-up"></i></span>
                                <span>3</span> new this month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Document Trends -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Document Activity Trends</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="trendPeriod" id="trend7days" checked>
                        <label class="btn btn-outline-primary" for="trend7days">7 Days</label>
                        
                        <input type="radio" class="btn-check" name="trendPeriod" id="trend30days">
                        <label class="btn btn-outline-primary" for="trend30days">30 Days</label>
                        
                        <input type="radio" class="btn-check" name="trendPeriod" id="trend12months">
                        <label class="btn btn-outline-primary" for="trend12months">12 Months</label>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Status Distribution -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Performance -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Department Performance</h5>
                    <a href="{{ route('analytics.department-performance') }}" class="btn btn-sm btn-outline-primary">
                        View Details
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Total Documents</th>
                                    <th>Completed</th>
                                    <th>Pending</th>
                                    <th>Completion Rate</th>
                                    <th>Avg. Processing Time</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody id="departmentPerformanceTable">
                                @foreach($departmentData as $data)
                                <tr>
                                    <td class="fw-semibold">{{ $data['name'] }}</td>
                                    <td>{{ number_format($data['total']) }}</td>
                                    <td class="text-success">{{ number_format($data['completed']) }}</td>
                                    <td class="text-warning">{{ number_format($data['pending']) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $data['completion_rate'] }}%</span>
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ $data['completion_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>3.2 days</td>
                                    <td>
                                        @if($data['completion_rate'] >= 80)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($data['completion_rate'] >= 60)
                                            <span class="badge bg-warning">Good</span>
                                        @else
                                            <span class="badge bg-danger">Needs Improvement</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Types & Priority Analysis -->
    <div class="row g-4">
        <!-- Document Types -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Popular Document Types</h5>
                </div>
                <div class="card-body">
                    @foreach($documentTypeData as $type)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ $type->name }}</h6>
                            <small class="text-muted">{{ $type->documents_count }} documents</small>
                        </div>
                        <div class="text-end">
                            <div class="progress" style="width: 100px; height: 6px;">
                                <div class="progress-bar bg-primary" 
                                     style="width: {{ $documentTypeData->max('documents_count') > 0 ? ($type->documents_count / $documentTypeData->max('documents_count')) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Priority Distribution -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Priority Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusData['pending'] ?? 0 }},
                    {{ $statusData['in_progress'] ?? 0 }},
                    {{ $statusData['completed'] ?? 0 }},
                    {{ $statusData['cancelled'] ?? 0 }}
                ],
                backgroundColor: [
                    '#f59e0b',
                    '#3b82f6',
                    '#10b981',
                    '#ef4444'
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

    // Priority Distribution Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    const priorityChart = new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['Low', 'Normal', 'High', 'Urgent'],
            datasets: [{
                label: 'Documents',
                data: [
                    {{ $priorityData['low'] ?? 0 }},
                    {{ $priorityData['normal'] ?? 0 }},
                    {{ $priorityData['high'] ?? 0 }},
                    {{ $priorityData['urgent'] ?? 0 }}
                ],
                backgroundColor: [
                    '#10b981',
                    '#6b7280',
                    '#f59e0b',
                    '#ef4444'
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

    // Trends Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    let trendsChart;

    function loadTrendsChart(period = '12months') {
        fetch(`/api/analytics/charts/monthly-documents`)
            .then(response => response.json())
            .then(data => {
                if (trendsChart) {
                    trendsChart.destroy();
                }

                trendsChart = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.month),
                        datasets: [{
                            label: 'Documents Created',
                            data: data.map(item => item.count),
                            borderColor: '#1e40af',
                            backgroundColor: 'rgba(30, 64, 175, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
            })
            .catch(error => {
                console.error('Error loading trends chart:', error);
            });
    }

    // Load initial chart
    loadTrendsChart();

    // Period selector handlers
    document.querySelectorAll('input[name="trendPeriod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            loadTrendsChart(this.id.replace('trend', ''));
        });
    });

    // Export functionality
    function exportData(type, format) {
        const button = event.target.closest('a') || event.target.closest('button');
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Exporting...';
        button.disabled = true;
        
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("analytics.export") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="type" value="${type}">
            <input type="hidden" name="format" value="${format}">
        `;
        
        document.body.appendChild(form);
        form.submit();
        
        // Reset button after delay
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            if (document.body.contains(form)) {
                document.body.removeChild(form);
            }
        }, 3000);
    }

    // Real-time data updates
    function updateMetrics() {
        fetch('/api/analytics/dashboard-data')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalDocuments').textContent = data.documents_today || 0;
                // Update other metrics as needed
            })
            .catch(error => {
                console.error('Error updating metrics:', error);
            });
    }

    // Update metrics every 30 seconds
    setInterval(updateMetrics, 30000);
</script>
@endpush