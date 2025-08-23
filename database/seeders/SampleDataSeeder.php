<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\User;
use App\Models\DocumentRoute;
use App\Models\DocumentHistory;
use App\Models\DocumentComment;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample documents for testing
        $documentTypes = DocumentType::all();
        $departments = Department::all();
        $users = User::all();

        for ($i = 1; $i <= 10; $i++) {
            $originDept = $departments->random();
            $currentDept = $departments->random();
            $creator = $users->where('department_id', $originDept->id)->first() ?: $users->first();

            $document = Document::create([
                'tracking_number' => Document::generateTrackingNumber(),
                'title' => 'Sample Document ' . $i,
                'description' => 'This is a sample document for testing purposes.',
                'document_type_id' => $documentTypes->random()->id,
                'origin_department_id' => $originDept->id,
                'current_department_id' => $currentDept->id,
                'created_by' => $creator->id,
                'priority' => collect(['low', 'normal', 'high', 'urgent'])->random(),
                'status' => collect(['pending', 'in_progress', 'completed'])->random(),
                'date_received' => now()->subDays(rand(1, 30)),
                'target_completion_date' => now()->addDays(rand(1, 14)),
                'is_confidential' => rand(0, 1),
                'remarks' => 'Sample remarks for document ' . $i,
            ]);

            // Create sample route
            DocumentRoute::create([
                'document_id' => $document->id,
                'from_department_id' => $originDept->id,
                'to_department_id' => $currentDept->id,
                'routed_by' => $creator->id,
                'routing_purpose' => 'For review and processing',
                'status' => 'received',
                'routed_at' => now()->subDays(rand(1, 5)),
                'received_at' => now()->subDays(rand(0, 3)),
            ]);

            // Create sample history
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => $creator->id,
                'action' => 'created',
                'description' => 'Document created',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Laravel Seeder',
            ]);

            // Create sample comment
            DocumentComment::create([
                'document_id' => $document->id,
                'user_id' => $users->random()->id,
                'comment' => 'This document requires immediate attention.',
                'type' => 'instruction',
                'is_internal' => true,
            ]);
        }
    }
}
