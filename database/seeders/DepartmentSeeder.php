<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Municipal System Administrator',
                'code' => 'SYSADMIN',
                'description' => 'Office of System Administrator',
                'head_name' => 'System Administrator',
                'is_active' => true,
            ],
            [
                'name' => 'Mayor\'s Office',
                'code' => 'MAYOR',
                'description' => 'Office of the Municipal Mayor',
                'head_name' => 'Hon. Municipal Mayor',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Secretary',
                'code' => 'MUNSEC',
                'description' => 'Municipal Secretary Office',
                'head_name' => 'Municipal Secretary',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Treasurer',
                'code' => 'MUNTREAS',
                'description' => 'Municipal Treasurer Office',
                'head_name' => 'Municipal Treasurer',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Assessor',
                'code' => 'MUNASS',
                'description' => 'Municipal Assessor Office',
                'head_name' => 'Municipal Assessor',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Planning and Development Office',
                'code' => 'MPDO',
                'description' => 'Municipal Planning and Development Office',
                'head_name' => 'MPDO Head',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Engineer\'s Office',
                'code' => 'MEO',
                'description' => 'Municipal Engineer\'s Office',
                'head_name' => 'Municipal Engineer',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Health Office',
                'code' => 'MHO',
                'description' => 'Municipal Health Office',
                'head_name' => 'Municipal Health Officer',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Social Welfare and Development Office',
                'code' => 'MSWDO',
                'description' => 'Municipal Social Welfare and Development Office',
                'head_name' => 'MSWDO Head',
                'is_active' => true,
            ],
            [
                'name' => 'Municipal Agriculture Office',
                'code' => 'MAO',
                'description' => 'Municipal Agriculture Office',
                'head_name' => 'Municipal Agriculturist',
                'is_active' => true,
            ],
            [
                'name' => 'Business Permit and Licensing Office',
                'code' => 'BPLO',
                'description' => 'Business Permit and Licensing Office',
                'head_name' => 'BPLO Head',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resource Management Office',
                'code' => 'HRMO',
                'description' => 'Human Resource Management Office',
                'head_name' => 'HRMO Head',
                'is_active' => true,
            ],
            [
                'name' => 'General Services Office',
                'code' => 'GSO',
                'description' => 'General Services Office',
                'head_name' => 'GSO Head',
                'is_active' => true,
            ],
            [
                'name' => 'Records Management Office',
                'code' => 'RMO',
                'description' => 'Records Management Office',
                'head_name' => 'Records Officer',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
