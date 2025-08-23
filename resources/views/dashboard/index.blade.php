@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-header')
    <div class="row align-items-start">
        <div class="col-12 col-lg-8">
            <h1 class="h3 mb-2 mb-lg-0">Dashboard</h1>
            <p class="text-muted mb-3 mb-lg-0">Welcome back, {{ Auth::user()->name }}</p>
        </div>
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                @can('create', App\Models\Document::class)
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">New Document</span>
                        <span class="d-sm-none">New Doc</span>
                    </a>
                @endcan
                <button class="btn btn-outline-secondary" onclick="refreshDashboard()" id="refreshBtn">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    <span class="d-none d-sm-inline">Refresh</span>
                    <span class="d-sm-none">Sync</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Mobile-first dashboard styles */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }
        
        .page-header .row {
            gap: 0.5rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 0.75rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
        
        .card-header {
            padding: 1rem 0.75rem;
        }
        
        .h4 {
            font-size: 1.5rem;
        }
        
        .h3 {
            font-size: 1.75rem;
        }
    }
    
    /* Enhanced stat cards for mobile */
    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--bs-primary) 0%, var(--bs-info) 100%);
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 576px) {
        .stat-card {
            margin-bottom: 1rem;
        }
    }
    
    /* Mobile-optimized table */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            white-space: nowrap;
        }
        
        .table th:nth-child(n+4),
        .table td:nth-child(n+4) {
            display: none;
        }
        
        .table-mobile-stack {
            display: block;
        }
        
        .table-mobile-stack tbody,
        .table-mobile-stack tr,
        .table-mobile-stack td {
            display: block;
            width: 100%;
        }
        
        .table-mobile-stack tr {
            background: white;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .table-mobile-stack tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .table-mobile-stack td {
            border: none;
            padding: 0.25rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-mobile-stack td::before {
            content: attr(data-label) ": ";
            font-weight: 600;
            color: #6b7280;
            flex-shrink: 0;
            margin-right: 1rem;
        }
        
        .table-mobile-stack thead {
            display: none;
        }
    }
    
    /* Mobile chart containers */
    @media (max-width: 768px) {
        .chart-container {
            height: 250px !important;
        }
        
        .chart-container canvas {
            max-height: 220px !important;
        }
    }
    
    @media (max-width: 576px) {
        .chart-container {
            height: 200px !important;
        }
        
        .chart-container canvas {
            max-height: 170px !important;
        }
    }
    
    /* Quick actions mobile layout */
    .quick-actions-mobile {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    @media (max-width: 576px) {
        .quick-actions-mobile {
            grid-template-columns: 1fr;
        }
    }
    
    /* Pending routes mobile layout */
    .pending-route-item {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-left: 3px solid #f59e0b;
        transition: all 0.2s ease;
    }
    
    .pending-route-item:hover {
        background: #f1f5f9;
        transform: translateX(2px);
    }
    
    @media (max-width: 576px) {
        .pending-route-item {
            padding: 0.75rem;
        }
    }
    
    /* Status badges mobile optimization */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    @media (max-width: 576px) {
        .status-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }
    
    /* Loading states */
    .btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .btn.loading .bi-arrow-clockwise {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Swipe actions for mobile */
    @media (max-width: 768px) {
        .swipe-item {
            position: relative;
            overflow: hidden;
        }
        
        .swipe-actions {
            position: absolute;
            right: -100px;
            top: 0;
            bottom: 0;
            width: 100px;
            background: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: right 0.3s ease;
        }
        
        .swipe-item.swiped .swipe-actions {
            right: 0;
        }
    }
    
    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        .stat-card,
        .pending-route-item,
        .table-mobile-stack tr {
            transition: none;
        }
        
        .stat-card:hover,
        .pending-route-item:hover,
        .table-mobile-stack tr:hover {
            transform: none;
        }
    }
    
    /* Toast notifications positioning */
    .toast-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1100;
    }
    
    @media (max-width: 576px) {
        .toast-container {
            top: 0.5rem;
            right: 0.5rem;
            left: 0.5rem;
        }
        
        .toast {
            width: 100%;
        }
    }
    
    /* Pull to refresh indicator */
    .pull-to-refresh {
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%) translateY(-100%);
        background: rgba(30, 64, 175, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0 0 0.5rem 0.5rem;
        font-size: 0.875rem;
        transition: transform 0.3s ease;
        z-index: 1050;
    }
    
    .pull-to-refresh.show {
        transform: translateX(-50%) translateY(0);
    }
</style>
@endpush

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-primary text-uppercase mb-1">
                                <span class="d-none d-sm-inline">Total Documents</span>
                                <span class="d-sm-none">Total</span>
                            </div>
                            <div class="h4 fw-bold mb-0">{{ number_format($stats['total_documents']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h4 fw-bold mb-0">{{ number_format($stats['pending_documents']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-info text-uppercase mb-1">
                                <span class="d-none d-sm-inline">In Progress</span>
                                <span class="d-sm-none">Active</span>
                            </div>
                            <div class="h4 fw-bold mb-0">{{ number_format($stats['in_progress_documents']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-repeat text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-success text-uppercase mb-1">Completed</div>
                            <div class="h4 fw-bold mb-0">{{ number_format($stats['completed_documents']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Monthly Documents Chart -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="d-none d-sm-inline">Document Creation Trend</span>
                        <span class="d-sm-none">Creation Trend</span>
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleChartType('monthly')" id="monthlyToggle">
                        <i class="bi bi-bar-chart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyDocumentsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="d-none d-sm-inline">Priority Distribution</span>
                        <span class="d-sm-none">Priorities</span>
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleChartType('priority')" id="priorityToggle">
                        <i class="bi bi-pie-chart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priorityChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-3 g-md-4">
        <!-- Recent Documents -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Documents</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleTableView()" id="tableToggle">
                            <i class="bi bi-list"></i>
                        </button>
                        <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary">
                            <span class="d-none d-sm-inline">View All</span>
                            <span class="d-sm-none">All</span>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($recentDocuments->count() > 0)
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tracking #</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentDocuments as $document)
                                        <tr onclick="navigateToDocument('{{ route('documents.show', $document->id) }}')"
                                            style="cursor: pointer;">
                                            <td class="fw-semibold">{{ $document->tracking_number }}</td>
                                            <td>
                                                {{ Str::limit($document->title, 40) }}
                                                @if ($document->is_confidential)
                                                    <i class="bi bi-shield-lock text-warning ms-1" title="Confidential"></i>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $document->documentType->name }}</td>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-md-none p-3" id="mobileDocuments">
                            @foreach ($recentDocuments as $document)
                                <div class="card mb-3 swipe-item" 
                                     onclick="navigateToDocument('{{ route('documents.show', $document->id) }}')"
                                     style="cursor: pointer;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-primary mb-0">{{ $document->tracking_number }}</h6>
                                            <span class="status-badge status-{{ $document->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                            </span>
                                        </div>
                                        <p class="mb-2">
                                            {{ Str::limit($document->title, 60) }}
                                            @if ($document->is_confidential)
                                                <i class="bi bi-shield-lock text-warning ms-1" title="Confidential"></i>
                                            @endif
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $document->documentType->name }}</small>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-flag priority-{{ $document->priority }}"></i>
                                                <small class="text-muted">{{ $document->created_at->format('M d') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swipe-actions">
                                        <i class="bi bi-eye"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No documents yet</h5>
                            <p class="text-muted">Create your first document to get started.</p>
                            @can('create', App\Models\Document::class)
                                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create Document
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Routes & Quick Actions -->
        <div class="col-12 col-xl-4">
            <!-- Pending Routes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="d-none d-sm-inline">Pending Routes</span>
                        <span class="d-sm-none">Pending</span>
                    </h5>
                    @if ($pendingRoutes->count() > 0)
                        <span class="badge bg-warning">{{ $pendingRoutes->count() }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($pendingRoutes->count() > 0)
                        @foreach ($pendingRoutes->take(5) as $route)
                            <div class="pending-route-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $route->document->tracking_number }}</h6>
                                        <p class="text-muted mb-1 small">
                                            <i class="bi bi-building me-1"></i>{{ $route->fromDepartment->name }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $route->routed_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="receiveDocument('{{ $route->id }}', this)">
                                            <i class="bi bi-check"></i>
                                            <span class="d-none d-sm-inline ms-1">Receive</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if ($pendingRoutes->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">{{ $pendingRoutes->count() - 5 }} more pending routes</small>
                            </div>
                        @endif
                        <a href="{{ route('document-routes.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-3">
                            <span class="d-none d-sm-inline">View All Routes</span>
                            <span class="d-sm-none">View All</span>
                        </a>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">All caught up!</p>
                            <small class="text-muted">No pending routes</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions-mobile">
                        @can('create', App\Models\Document::class)
                            <a href="{{ route('documents.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                <span class="d-none d-lg-inline">New Document</span>
                                <span class="d-lg-none">New Doc</span>
                            </a>
                        @endcan

                        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-search me-2"></i>
                            <span class="d-none d-lg-inline">Search Documents</span>
                            <span class="d-lg-none">Search</span>
                        </a>

                        @can('view-reports')
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-info">
                                <i class="bi bi-graph-up me-2"></i>
                                <span class="d-none d-lg-inline">View Reports</span>
                                <span class="d-lg-none">Reports</span>
                            </a>
                        @endcan

                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-warning position-relative">
                            <i class="bi bi-bell me-2"></i>
                            <span class="d-none d-lg-inline">Notifications</span>
                            <span class="d-lg-none">Alerts</span>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifBadge" style="display: none;">
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pull to Refresh Indicator -->
    <div class="pull-to-refresh" id="pullToRefresh">
        <i class="bi bi-arrow-clockwise me-2"></i>Pull to refresh
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let monthlyChart, priorityChart;
            let isMonthlyBarChart = false;
            let isPriorityBarChart = false;
            
            // Initialize charts
            initializeCharts();
            
            // Mobile-specific features
            if (window.innerWidth <= 768) {
                initializeMobileFeatures();
            }
            
            function initializeCharts() {
                // Monthly Documents Chart
                const monthlyCtx = document.getElementById('monthlyDocumentsChart').getContext('2d');
                monthlyChart = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($monthlyData['labels']) !!},
                        datasets: [{
                            label: 'Documents Created',
                            data: {!! json_encode($monthlyData['data']) !!},
                            borderColor: '#1e40af',
                            backgroundColor: 'rgba(30, 64, 175, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#1e40af',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
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
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#6b7280'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#6b7280'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });

                // Priority Distribution Chart
                const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                priorityChart = new Chart(priorityCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Low', 'Normal', 'High', 'Urgent'],
                        datasets: [{
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
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: {
                                        size: window.innerWidth <= 576 ? 10 : 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                cornerRadius: 8
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
            
            function initializeMobileFeatures() {
                // Pull to refresh
                let startY = 0;
                let currentY = 0;
                let isPulling = false;
                
                document.addEventListener('touchstart', function(e) {
                    if (window.scrollY === 0) {
                        startY = e.touches[0].pageY;
                        isPulling = true;
                    }
                }, { passive: true });
                
                document.addEventListener('touchmove', function(e) {
                    if (isPulling) {
                        currentY = e.touches[0].pageY;
                        const pullDistance = currentY - startY;
                        
                        if (pullDistance > 0 && pullDistance < 100) {
                            const pullIndicator = document.getElementById('pullToRefresh');
                            pullIndicator.style.transform = `translateX(-50%) translateY(${pullDistance - 100}px)`;
                            
                            if (pullDistance > 60) {
                                pullIndicator.classList.add('show');
                            }
                        }
                    }
                }, { passive: true });
                
                document.addEventListener('touchend', function(e) {
                    if (isPulling) {
                        const pullDistance = currentY - startY;
                        const pullIndicator = document.getElementById('pullToRefresh');
                        
                        if (pullDistance > 60) {
                            refreshDashboard();
                        }
                        
                        pullIndicator.style.transform = 'translateX(-50%) translateY(-100%)';
                        pullIndicator.classList.remove('show');
                        isPulling = false;
                    }
                }, { passive: true });
                
                // Swipe gestures for document cards
                initializeSwipeGestures();
            }
            
            function initializeSwipeGestures() {
                const swipeItems = document.querySelectorAll('.swipe-item');
                
                swipeItems.forEach(item => {
                    let startX = 0;
                    let currentX = 0;
                    let isSwiping = false;
                    
                    item.addEventListener('touchstart', function(e) {
                        startX = e.touches[0].pageX;
                        isSwiping = true;
                    }, { passive: true });
                    
                    item.addEventListener('touchmove', function(e) {
                        if (isSwiping) {
                            currentX = e.touches[0].pageX;
                            const swipeDistance = startX - currentX;
                            
                            if (swipeDistance > 0 && swipeDistance < 100) {
                                item.style.transform = `translateX(-${swipeDistance}px)`;
                            }
                        }
                    }, { passive: true });
                    
                    item.addEventListener('touchend', function(e) {
                        if (isSwiping) {
                            const swipeDistance = startX - currentX;
                            
                            if (swipeDistance > 50) {
                                item.classList.add('swiped');
                            } else {
                                item.style.transform = '';
                            }
                            
                            isSwiping = false;
                        }
                    }, { passive: true });
                });
            }
            
            // Chart toggle functions
            window.toggleChartType = function(chartType) {
                if (chartType === 'monthly') {
                    isMonthlyBarChart = !isMonthlyBarChart;
                    monthlyChart.destroy();
                    
                    const monthlyCtx = document.getElementById('monthlyDocumentsChart').getContext('2d');
                    monthlyChart = new Chart(monthlyCtx, {
                        type: isMonthlyBarChart ? 'bar' : 'line',
                        data: {
                            labels: {!! json_encode($monthlyData['labels']) !!},
                            datasets: [{
                                label: 'Documents Created',
                                data: {!! json_encode($monthlyData['data']) !!},
                                borderColor: '#1e40af',
                                backgroundColor: isMonthlyBarChart ? '#1e40af' : 'rgba(30, 64, 175, 0.1)',
                                borderWidth: isMonthlyBarChart ? 0 : 3,
                                fill: !isMonthlyBarChart,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 }
                                }
                            }
                        }
                    });
                    
                    document.getElementById('monthlyToggle').innerHTML = 
                        `<i class="bi bi-${isMonthlyBarChart ? 'graph-up' : 'bar-chart'}"></i>`;
                } else if (chartType === 'priority') {
                    isPriorityBarChart = !isPriorityBarChart;
                    priorityChart.destroy();
                    
                    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                    priorityChart = new Chart(priorityCtx, {
                        type: isPriorityBarChart ? 'bar' : 'doughnut',
                        data: {
                            labels: ['Low', 'Normal', 'High', 'Urgent'],
                            datasets: [{
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
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: isPriorityBarChart ? 'top' : 'bottom'
                                }
                            },
                            scales: isPriorityBarChart ? {
                                y: { beginAtZero: true }
                            } : {}
                        }
                    });
                    
                    document.getElementById('priorityToggle').innerHTML = 
                        `<i class="bi bi-${isPriorityBarChart ? 'pie-chart' : 'bar-chart'}"></i>`;
                }
            };
            
            // Table view toggle for mobile
            window.toggleTableView = function() {
                const desktopTable = document.querySelector('.table-responsive');
                const mobileCards = document.getElementById('mobileDocuments');
                const toggleBtn = document.getElementById('tableToggle');
                
                if (desktopTable.classList.contains('d-none')) {
                    desktopTable.classList.remove('d-none');
                    mobileCards.classList.add('d-none');
                    toggleBtn.innerHTML = '<i class="bi bi-grid"></i>';
                } else {
                    desktopTable.classList.add('d-none');
                    mobileCards.classList.remove('d-none');
                    toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
                }
            };
            
            // Navigation function
            window.navigateToDocument = function(url) {
                if ('vibrate' in navigator) {
                    navigator.vibrate(50); // Haptic feedback
                }
                window.location.href = url;
            };

            // Receive Document Function with enhanced mobile feedback
            window.receiveDocument = function(routeId, buttonElement) {
                if (confirm('Are you sure you want to receive this document?')) {
                    // Add loading state
                    const originalText = buttonElement.innerHTML;
                    buttonElement.disabled = true;
                    buttonElement.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                    
                    fetch(`/routes/${routeId}/receive`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': window.Laravel.csrfToken,
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast('Document received successfully!', 'success');
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showToast('Error: ' + data.message, 'danger');
                                buttonElement.disabled = false;
                                buttonElement.innerHTML = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('An error occurred while receiving the document.', 'danger');
                            buttonElement.disabled = false;
                            buttonElement.innerHTML = originalText;
                        });
                }
            };

            // Enhanced Refresh Dashboard
            window.refreshDashboard = function() {
                const refreshBtn = document.getElementById('refreshBtn');
                refreshBtn.classList.add('loading');
                
                showToast('Refreshing dashboard...', 'info');
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            };
            
            // Toast notification function
            function showToast(message, type = 'info') {
                let container = document.querySelector('.toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'toast-container';
                    document.body.appendChild(container);
                }
                
                const toastId = 'toast-' + Date.now();
                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `toast align-items-center text-white bg-${type} border-0`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                
                container.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast, {
                    autohide: true,
                    delay: type === 'danger' ? 6000 : 3000
                });
                bsToast.show();
                
                toast.addEventListener('hidden.bs.toast', function() {
                    toast.remove();
                });
            }
            
            // Load notification count
            fetch('/api/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notifBadge');
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading notification count:', error);
                });
            
            // Auto-refresh every 5 minutes
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    fetch('/api/dashboard/stats')
                        .then(response => response.json())
                        .then(data => {
                            // Update stats without full page reload
                            console.log('Dashboard stats updated');
                        })
                        .catch(error => {
                            console.error('Error updating stats:', error);
                        });
                }
            }, 300000); // 5 minutes
        });
    </script>
@endpush