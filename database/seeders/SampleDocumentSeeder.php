<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\User;
use App\Models\DocumentRoute;
use App\Models\DocumentHistory;
use Illuminate\Database\Seeder;

class SampleDocumentSeeder extends Seeder
{
    public function run()
    {
        // Check if we have the required data
        $documentType = DocumentType::first();
        $department = Department::first();
        $user = User::first();

        if (!$documentType || !$department || !$user) {
            $this->command->info('Skipping sample documents - missing required data');
            return;
        }

        // Create 5 sample documents for testing
        for ($i = 1; $i <= 5; $i++) {
            $document = Document::create([
                'tracking_number' => 'MDJ-' . date('Ym') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'title' => 'Sample Document ' . $i,
                'description' => 'This is a sample document created for testing the tracking system.',
                'document_type_id' => $documentType->id,
                'origin_department_id' => $department->id,
                'current_department_id' => $department->id,
                'created_by' => $user->id,
                'priority' => ['low', 'normal', 'high', 'urgent'][array_rand(['low', 'normal', 'high', 'urgent'])],
                'status' => ['pending', 'in_progress', 'completed'][array_rand(['pending', 'in_progress', 'completed'])],
                'date_received' => now()->subDays(rand(1, 30)),
                'target_completion_date' => now()->addDays(rand(1, 14)),
                'is_confidential' => $i === 5, // Make the last one confidential for testing
                'remarks' => 'Sample document for testing purposes.',
            ]);

            $this->command->info('Created sample document: ' . $document->tracking_number);
        }
    }
}