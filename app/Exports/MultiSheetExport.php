<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function sheets(): array
    {
        return [
            'Documents' => new DocumentsExport($this->filters),
            'Department Performance' => new DepartmentPerformanceExport(
                $this->filters['date_from'] ?? null,
                $this->filters['date_to'] ?? null
            ),
            'Users' => new UsersExport($this->filters),
        ];
    }
}