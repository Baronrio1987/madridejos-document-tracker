<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Document::with(['documentType', 'originDepartment', 'currentDepartment', 'creator']);
        
        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (!empty($this->filters['priority'])) {
            $query->where('priority', $this->filters['priority']);
        }
        
        if (!empty($this->filters['document_type_id'])) {
            $query->where('document_type_id', $this->filters['document_type_id']);
        }
        
        if (!empty($this->filters['department_id'])) {
            $query->where('current_department_id', $this->filters['department_id']);
        }
        
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function headings(): array
    {
        return [
            'Tracking Number',
            'Title',
            'Description',
            'Document Type',
            'Origin Department',
            'Current Department',
            'Status',
            'Priority',
            'Created By',
            'Date Received',
            'Target Completion',
            'Actual Completion',
            'Created Date',
            'Is Confidential',
            'Remarks'
        ];
    }
    
    public function map($document): array
    {
        return [
            $document->tracking_number,
            $document->title,
            $document->description,
            $document->documentType->name,
            $document->originDepartment->name,
            $document->currentDepartment->name,
            ucfirst(str_replace('_', ' ', $document->status)),
            ucfirst($document->priority),
            $document->creator->name,
            $document->date_received->format('Y-m-d'),
            $document->target_completion_date ? $document->target_completion_date->format('Y-m-d') : '',
            $document->actual_completion_date ? $document->actual_completion_date->format('Y-m-d') : '',
            $document->created_at->format('Y-m-d H:i:s'),
            $document->is_confidential ? 'Yes' : 'No',
            $document->remarks
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '1e40af'],
                ],
            ],
        ];
    }
    
    /**
     * Set sheet title
     */
    public function title(): string
    {
        return 'Documents Report';
    }
    
    /**
     * Register events for additional formatting
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set header row height
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(25);
                
                // Apply borders to all data
                $cellRange = 'A1:O' . ($this->collection()->count() + 1);
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                
                // Freeze header row
                $event->sheet->getDelegate()->freezePane('A2');
                
                // Set column widths
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20); // Tracking Number
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(30); // Title
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(40); // Description
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(30); // Remarks
            },
        ];
    }
}