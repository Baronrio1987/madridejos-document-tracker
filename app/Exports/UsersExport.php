<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function query()
    {
        $query = User::with(['department']);
        
        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }
        
        if (!empty($this->filters['role'])) {
            $query->where('role', $this->filters['role']);
        }
        
        if (!empty($this->filters['status'])) {
            $isActive = $this->filters['status'] === 'active';
            $query->where('is_active', $isActive);
        }
        
        return $query->orderBy('name');
    }
    
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Employee ID',
            'Department',
            'Role',
            'Position',
            'Phone',
            'Status',
            'Last Login',
            'Created Date'
        ];
    }
    
    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->employee_id,
            $user->department ? $user->department->name : 'N/A',
            ucfirst(str_replace('_', ' ', $user->role)),
            $user->position,
            $user->phone,
            $user->is_active ? 'Active' : 'Inactive',
            $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
            $user->created_at->format('Y-m-d H:i:s')
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