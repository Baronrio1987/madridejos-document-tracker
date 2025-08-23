<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remove attachments column since we now have separate table
            $table->dropColumn('attachments');
            
            // Add some useful fields
            $table->string('external_reference')->nullable()->after('tracking_number'); // for external tracking numbers
            $table->integer('estimated_processing_days')->nullable()->after('target_completion_date');
            $table->decimal('urgency_score', 3, 1)->default(1.0)->after('priority'); // calculated urgency
            $table->json('metadata')->nullable()->after('remarks'); // for additional custom fields
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->json('attachments')->nullable();
            $table->dropColumn(['external_reference', 'estimated_processing_days', 'urgency_score', 'metadata']);
        });
    }
};
