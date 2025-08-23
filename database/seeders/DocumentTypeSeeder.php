<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $documentTypes = [
            [
                'name' => 'Memorandum',
                'code' => 'MEMO',
                'description' => 'Internal memorandum communications',
                'retention_period' => 365,
                'is_active' => true,
            ],
            [
                'name' => 'Ordinance',
                'code' => 'ORD',
                'description' => 'Municipal ordinances',
                'retention_period' => 3650, // 10 years
                'is_active' => true,
            ],
            [
                'name' => 'Resolution',
                'code' => 'RES',
                'description' => 'Municipal resolutions',
                'retention_period' => 1825, // 5 years
                'is_active' => true,
            ],
            [
                'name' => 'Letter',
                'code' => 'LTR',
                'description' => 'Official letters',
                'retention_period' => 730, // 2 years
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Request',
                'code' => 'PR',
                'description' => 'Purchase requests',
                'retention_period' => 1095, // 3 years
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Order',
                'code' => 'PO',
                'description' => 'Purchase orders',
                'retention_period' => 1095, // 3 years
                'is_active' => true,
            ],
            [
                'name' => 'Voucher',
                'code' => 'VOUCH',
                'description' => 'Financial vouchers',
                'retention_period' => 1825, // 5 years
                'is_active' => true,
            ],
            [
                'name' => 'Report',
                'code' => 'RPT',
                'description' => 'Official reports',
                'retention_period' => 730, // 2 years
                'is_active' => true,
            ],
            [
                'name' => 'Application',
                'code' => 'APP',
                'description' => 'Various applications',
                'retention_period' => 365, // 1 year
                'is_active' => true,
            ],
            [
                'name' => 'Notice',
                'code' => 'NOT',
                'description' => 'Official notices',
                'retention_period' => 365, // 1 year
                'is_active' => true,
            ],
            [
                'name' => 'Contract',
                'code' => 'CON',
                'description' => 'Contracts and agreements',
                'retention_period' => 3650, // 10 years
                'is_active' => true,
            ],
            [
                'name' => 'Permit',
                'code' => 'PER',
                'description' => 'Various permits',
                'retention_period' => 1095, // 3 years
                'is_active' => true,
            ],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::create($documentType);
        }
    }
}
