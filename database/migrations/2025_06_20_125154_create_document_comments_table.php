<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment');
            $table->enum('type', ['general', 'instruction', 'feedback', 'approval', 'rejection'])->default('general');
            $table->boolean('is_internal')->default(true); // internal vs external comments
            $table->foreignId('parent_id')->nullable()->constrained('document_comments'); // for reply comments
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_comments');
    }
};
