<?php

namespace Database\Seeders;

use App\Models\RoutingTemplate;
use App\Models\DocumentType;
use App\Models\Department;
use Illuminate\Database\Seeder;

class RoutingTemplateSeeder extends Seeder
{
    public function run()
    {
        $mayorOffice = Department::where('code', 'MAYOR')->first()->id;
        $munSec = Department::where('code', 'MUNSEC')->first()->id;
        $munTreas = Department::where('code', 'MUNTREAS')->first()->id;
        $mpdo = Department::where('code', 'MPDO')->first()->id;
        $rmo = Department::where('code', 'RMO')->first()->id;

        $templates = [
            [
                'name' => 'Standard Memorandum Route',
                'document_type_id' => DocumentType::where('code', 'MEMO')->first()->id,
                'route_sequence' => [$munSec, $mayorOffice, $rmo],
                'description' => 'Standard routing for memorandums',
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Request Route',
                'document_type_id' => DocumentType::where('code', 'PR')->first()->id,
                'route_sequence' => [$munSec, $munTreas, $mayorOffice, $rmo],
                'description' => 'Standard routing for purchase requests',
                'is_active' => true,
            ],
            [
                'name' => 'Ordinance Route',
                'document_type_id' => DocumentType::where('code', 'ORD')->first()->id,
                'route_sequence' => [$munSec, $mpdo, $mayorOffice, $rmo],
                'description' => 'Standard routing for ordinances',
                'is_active' => true,
            ],
            [
                'name' => 'Resolution Route',
                'document_type_id' => DocumentType::where('code', 'RES')->first()->id,
                'route_sequence' => [$munSec, $mayorOffice, $rmo],
                'description' => 'Standard routing for resolutions',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            RoutingTemplate::create($template);
        }
    }
}
