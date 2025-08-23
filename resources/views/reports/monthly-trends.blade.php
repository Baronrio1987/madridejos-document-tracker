@extends('layouts.app')

@section('title', 'Monthly Trends Report')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Monthly Trends</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Monthly Trends Report</h1>
            <p class="text-muted mb-0">Document creation and completion trends over time</p>
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
    <!-- Trends Chart -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Activity Trends (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Monthly Statistics</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Documents Created</th>
                            <th>Documents Completed</th>
                            <th>Completion Rate</th>
                            <th>Growth Rate</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $monthData)
                        @php
                            $prevMonth = $index > 0 ? $data[$index - 1] : null;
                            $growthRate = $prevMonth && $prevMonth['created'] > 0 
                                ? round((($monthData['created'] - $prevMonth['created']) / $prevMonth['created']) * 100, 1) 
                                : 0;
                            $completionRate = $monthData['created'] > 0 
                                ? round(($monthData['completed'] / $monthData['created']) * 100, 1) 
                                : 0;
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $monthData['month'] }}</td>
                            <td>{{ number_format($monthData['created']) }}</td>
                            <td class="text-success">{{ number_format($monthData['completed']) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $completionRate }}%</span>
                                    <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                        <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($growthRate > 0)
                                    <span class="text-success">
                                        <i class="bi bi-arrow-up me-1"></i>{{ $growthRate }}%
                                    </span>
                                @elseif($growthRate < 0)
                                    <span class="text-danger">
                                        <i class="bi bi-arrow-down me-1"></i>{{ abs($growthRate) }}%
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="bi bi-dash me-1"></i>0%
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($completionRate >= 90)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($completionRate >= 80)
                                    <span class="badge bg-info">Good</span>
                                @elseif($completionRate >= 70)
                                    <span class="badge bg-warning">Fair</span>
                                @else
                                    <span class="badge bg-danger">Poor</span>
                                @endif
                            </td>
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
    // Monthly Trends Chart
    const ctx = document.getElementById('trendsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(collect($data)->pluck('month')),
            datasets: [
                {
                    label: 'Documents Created',
                    data: @json(collect($data)->pluck('created')),
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Documents Completed',
                    data: @json(collect($data)->pluck('completed')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
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
</script>
@endpush
