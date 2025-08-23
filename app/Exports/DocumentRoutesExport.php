<?php

namespace App\Exports;

use App\Models\DocumentRoute;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentRoutesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function query()
    {
        $query = DocumentRoute::with(['document', 'fromDepartment', 'toDepartment', 'routedBy', 'receivedBy']);
        
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (!empty($this->filters['department_id'])) {
            $query->where(function($q) use ($filters) {
                $q->where('from_department_id', $this->filters['department_id'])
                  ->orWhere('to_department_id', $this->filters['department_id']);
            });
        }
        
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('routed_at', '>=', $this->filters['date_from']);
        }
        
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('routed_at', '<=', $this->filters['date_to']);
        }
        
        return $query->orderBy('routed_at', 'desc');
    }
    
    public function headings(): array
    {
        return [
            'Document Tracking #',
            'Document Title',
            'From Department',
            'To Department',
            'Routing Purpose',
            'Instructions',
            'Status',
            'Routed By',
            'Received By',
            'Routed Date',
            'Received Date',
            'Processed Date',
            'Processing Time (Hours)',
            'Remarks'
        ];
    }
    
    public function map($route): array
    {
        $processingTime = null;
        if ($route->received_at && $route->processed_at) {
            $processingTime = $route->received_at->diffInHours($route->processed_at);
        }
        
        return [
            $route->document->tracking_number,
            $route->document->title,
            $route->fromDepartment->name,
            $route->toDepartment->name,
            $route->routing_purpose,
            $route->instructions,
            ucfirst($route->status),
            $route->routedBy->name,
            $route->receivedBy ? $route->receivedBy->name : '',
            $route->routed_at->format('Y-m-d H:i:s'),
            $route->received_at ? $route->received_at->format('Y-m-d H:i:s') : '',
            $route->processed_at ? $route->processed_at->format('Y-m-d H:i:s') : '',
            $processingTime,
            $route->remarks
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'E2E8F0'],
                ],
            ],
        ];
    }
}