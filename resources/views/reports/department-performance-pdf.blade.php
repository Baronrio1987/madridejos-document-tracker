<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Department Performance Report</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #666;
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: normal;
        }
        
        .header p {
            margin: 0;
            color: #888;
            font-size: 11px;
        }
        
        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            display: block;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .performance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .performance-table th,
        .performance-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .performance-table th {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        .performance-table td {
            font-size: 11px;
        }
        
        .performance-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .progress-bar {
            background-color: #e9ecef;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin: 2px 0;
        }
        
        .progress-fill {
            background-color: #10b981;
            height: 100%;
            border-radius: 4px;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-excellent { background-color: #10b981; color: white; }
        .badge-good { background-color: #f59e0b; color: white; }
        .badge-needs-improvement { background-color: #ef4444; color: white; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        
        .filters-applied {
            background-color: #f0f9ff;
            border: 1px solid #bfdbfe;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .filters-applied strong {
            color: #1e40af;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Municipality of Madridejos</h1>
        <h2>Department Performance Report</h2>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>
    
    <!-- Filters Applied -->
    @if($request->filled('date_from') || $request->filled('date_to'))
    <div class="filters-applied">
        <strong>Report Period:</strong>
        @if($request->filled('date_from'))
            From {{ \Carbon\Carbon::parse($request->date_from)->format('F d, Y') }}
        @endif
        @if($request->filled('date_to'))
            To {{ \Carbon\Carbon::parse($request->date_to)->format('F d, Y') }}
        @endif
        @if(!$request->filled('date_from') && !$request->filled('date_to'))
            All Time
        @endif
    </div>
    @endif
    
    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-item">
            <span class="stat-number">{{ count($performanceData) }}</span>
            <div class="stat-label">Total Departments</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ collect($performanceData)->sum('total_documents') }}</span>
            <div class="stat-label">Total Documents</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ collect($performanceData)->sum('completed_documents') }}</span>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ collect($performanceData)->sum('overdue_documents') }}</span>
            <div class="stat-label">Overdue</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ round(collect($performanceData)->avg('completion_rate'), 1) }}%</span>
            <div class="stat-label">Avg Completion Rate</div>
        </div>
    </div>
    
    <!-- Performance Table -->
    <table class="performance-table">
        <thead>
            <tr>
                <th>Department</th>
                <th class="text-center">Total</th>
                <th class="text-center">Pending</th>
                <th class="text-center">Completed</th>
                <th class="text-center">Overdue</th>
                <th class="text-center">Completion Rate</th>
                <th class="text-center">Performance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceData as $data)
            <tr>
                <td>
                    <strong>{{ $data['department']->name }}</strong><br>
                    <small style="color: #666;">{{ $data['department']->code }}</small>
                </td>
                <td class="text-center">{{ number_format($data['total_documents']) }}</td>
                <td class="text-center">{{ number_format($data['pending_documents']) }}</td>
                <td class="text-center">{{ number_format($data['completed_documents']) }}</td>
                <td class="text-center">{{ number_format($data['overdue_documents']) }}</td>
                <td class="text-center">
                    <div>{{ $data['completion_rate'] }}%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $data['completion_rate'] }}%"></div>
                    </div>
                </td>
                <td class="text-center">
                    @if($data['completion_rate'] >= 80)
                        <span class="badge badge-excellent">Excellent</span>
                    @elseif($data['completion_rate'] >= 60)
                        <span class="badge badge-good">Good</span>
                    @else
                        <span class="badge badge-needs-improvement">Needs Improvement</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Performance Analysis -->
    <div style="margin-top: 30px;">
        <h3 style="color: #1e40af; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Performance Analysis</h3>
        
        @php
            $excellentDepts = collect($performanceData)->where('completion_rate', '>=', 80)->count();
            $goodDepts = collect($performanceData)->whereBetween('completion_rate', [60, 79])->count();
            $needsImprovementDepts = collect($performanceData)->where('completion_rate', '<', 60)->count();
            $totalDepts = count($performanceData);
        @endphp
        
        <div style="margin-bottom: 15px;">
            <strong>Performance Distribution:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Excellent Performance (≥80%): {{ $excellentDepts }} departments ({{ $totalDepts > 0 ? round(($excellentDepts / $totalDepts) * 100, 1) : 0 }}%)</li>
                <li>Good Performance (60-79%): {{ $goodDepts }} departments ({{ $totalDepts > 0 ? round(($goodDepts / $totalDepts) * 100, 1) : 0 }}%)</li>
                <li>Needs Improvement (<60%): {{ $needsImprovementDepts }} departments ({{ $totalDepts > 0 ? round(($needsImprovementDepts / $totalDepts) * 100, 1) : 0 }}%)</li>
            </ul>
        </div>
        
        @if($needsImprovementDepts > 0)
        <div style="background-color: #fef2f2; border: 1px solid #fecaca; padding: 10px; margin-bottom: 15px;">
            <strong style="color: #dc2626;">Departments Requiring Attention:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @foreach($performanceData as $data)
                    @if($data['completion_rate'] < 60)
                    <li style="color: #dc2626;">{{ $data['department']->name }} - {{ $data['completion_rate'] }}% completion rate</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif
        
        @if($excellentDepts > 0)
        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px;">
            <strong style="color: #059669;">Top Performing Departments:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                @foreach(collect($performanceData)->sortByDesc('completion_rate')->take(3) as $data)
                    @if($data['completion_rate'] >= 80)
                    <li style="color: #059669;">{{ $data['department']->name }} - {{ $data['completion_rate'] }}% completion rate</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>Municipality of Madridejos</strong><br>
            Document Tracking System
        </div>
        <div style="float: right; text-align: right;">
            Report generated by: {{ Auth::user()->name }}<br>
            {{ now()->format('F d, Y \a\t g:i A') }}
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>