<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('routing_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->json('route_sequence'); // Array of department IDs in order
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('routing_templates');
    }
};
