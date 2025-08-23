<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->string('signature_path')->nullable(); // for digital signatures
            $table->timestamp('approved_at')->nullable();
            $table->integer('approval_level')->default(1); // for multi-level approvals
            $table->boolean('is_final_approval')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_approvals');
    }
};
