<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $dateFrom;
    protected $dateTo;
    
    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }
    
    public function collection()
    {
        return Department::active()->get()->map(function ($department) {
            $query = Document::where('current_department_id', $department->id);
            
            if ($this->dateFrom) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            }
            
            if ($this->dateTo) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            }
            
            $total = $query->count();
            $pending = $query->where('status', 'pending')->count();
            $completed = $query->where('status', 'completed')->count();
            $overdue = $query->where('target_completion_date', '<', now())
                           ->whereNotIn('status', ['completed', 'cancelled'])
                           ->count();
            
            $department->stats = [
                'total' => $total,
                'pending' => $pending,
                'completed' => $completed,
                'overdue' => $overdue,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            ];
            
            return $department;
        });
    }
    
    public function headings(): array
    {
        return [
            'Department',
            'Department Code',
            'Total Documents',
            'Pending',
            'Completed',
            'Overdue',
            'Completion Rate (%)',
            'Department Head',
            'Active Users'
        ];
    }
    
    public function map($department): array
    {
        return [
            $department->name,
            $department->code,
            $department->stats['total'],
            $department->stats['pending'],
            $department->stats['completed'],
            $department->stats['overdue'],
            $department->stats['completion_rate'],
            $department->head_name,
            $department->users()->active()->count()
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