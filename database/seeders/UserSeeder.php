<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create super admin
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@madridejos.gov.ph',
            'password' => Hash::make('admin123'),
            'employee_id' => 'EMP-001',
            'department_id' => Department::where('code', 'SYSADMIN')->first()->id,
            'role' => 'admin',
            'position' => 'System Administrator',
            'phone' => '09123456789',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Mayor account
        User::create([
            'name' => 'Municipal Mayor',
            'email' => 'mayor@madridejos.gov.ph',
            'password' => Hash::make('mayor123'),
            'employee_id' => 'EMP-002',
            'department_id' => Department::where('code', 'MAYOR')->first()->id,
            'role' => 'department_head',
            'position' => 'Municipal Mayor',
            'phone' => '09123456790',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Municipal Secretary
        User::create([
            'name' => 'Municipal Secretary',
            'email' => 'secretary@madridejos.gov.ph',
            'password' => Hash::make('secretary123'),
            'employee_id' => 'EMP-003',
            'department_id' => Department::where('code', 'MUNSEC')->first()->id,
            'role' => 'department_head',
            'position' => 'Municipal Secretary',
            'phone' => '09123456791',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample department heads
        $departments = Department::whereNotIn('code', ['MAYOR', 'MUNSEC'])->get();
        
        foreach ($departments as $index => $department) {
            User::create([
                'name' => $department->head_name,
                'email' => strtolower($department->code) . '@madridejos.gov.ph',
                'password' => Hash::make('head123'),
                'employee_id' => 'EMP-' . str_pad($index + 4, 3, '0', STR_PAD_LEFT),
                'department_id' => $department->id,
                'role' => 'department_head',
                'position' => $department->head_name,
                'phone' => '0912345' . str_pad($index + 4, 4, '0', STR_PAD_LEFT),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create sample encoders
        $encoderDepts = Department::whereIn('code', ['MUNSEC', 'RMO', 'MAYOR'])->get();
        
        foreach ($encoderDepts as $index => $department) {
            User::create([
                'name' => 'Document Encoder ' . ($index + 1),
                'email' => 'encoder' . ($index + 1) . '@madridejos.gov.ph',
                'password' => Hash::make('encoder123'),
                'employee_id' => 'EMP-' . str_pad($index + 20, 3, '0', STR_PAD_LEFT),
                'department_id' => $department->id,
                'role' => 'encoder',
                'position' => 'Document Encoder',
                'phone' => '0912346' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
