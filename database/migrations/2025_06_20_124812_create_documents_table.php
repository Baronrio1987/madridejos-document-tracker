<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->foreignId('origin_department_id')->constrained('departments');
            $table->foreignId('current_department_id')->constrained('departments');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'archived'])->default('pending');
            $table->date('date_received');
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->json('attachments')->nullable(); // Store file paths
            $table->text('remarks')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
