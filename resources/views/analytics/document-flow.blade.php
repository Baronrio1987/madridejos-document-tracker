@extends('layouts.app')

@section('title', 'Document Flow Analytics')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('analytics.index') }}">Analytics</a></li>
    <li class="breadcrumb-item active">Document Flow</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Document Flow Analytics</h1>
            <p class="text-muted mb-0">Analyze document routing patterns and processing efficiency</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Flow Overview -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Most Common Document Routes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Documents</th>
                                    <th>Percentage</th>
                                    <th>Avg Processing Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flowData as $flow)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $flow->from_department }}</span>
                                            <i class="bi bi-arrow-right text-muted me-2"></i>
                                            <span>{{ $flow->to_department }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format($flow->count) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ number_format(($flow->count / $flowData->sum('count')) * 100, 1) }}%</span>
                                            <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: {{ ($flow->count / $flowData->max('count')) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">2.5 days</td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <!-- Processing Time Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Processing Time Analysis</h6>
                </div>
                <div class="card-body">
                    @foreach($processingTimes as $time)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ $time->department }}</h6>
                            <small class="text-muted">Average: {{ number_format($time->avg_processing_time, 1) }}h</small>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">
                                Min: {{ number_format($time->min_processing_time, 1) }}h<br>
                                Max: {{ number_format($time->max_processing_time, 1) }}h
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Time Chart -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Average Processing Time by Department</h5>
        </div>
        <div class="card-body">
            <canvas id="processingChart" height="100"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Processing Time Chart
    const ctx = document.getElementById('processingChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: @json($processingTimes->pluck('department')),
            datasets: [{
                label: 'Average Processing Time (hours)',
                data: @json($processingTimes->pluck('avg_processing_time')),
                backgroundColor: 'rgba(30, 64, 175, 0.8)',
                borderColor: '#1e40af',
                borderWidth: 1
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
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                }
            }
        }
    });
</script>
@endpush