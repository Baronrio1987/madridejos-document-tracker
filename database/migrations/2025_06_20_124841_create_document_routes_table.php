<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('from_department_id')->constrained('departments');
            $table->foreignId('to_department_id')->constrained('departments');
            $table->foreignId('routed_by')->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->text('routing_purpose')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['pending', 'received', 'processed', 'forwarded'])->default('pending');
            $table->timestamp('routed_at');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_routes');
    }
};
