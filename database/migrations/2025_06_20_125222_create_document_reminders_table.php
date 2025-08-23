<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('message');
            $table->datetime('remind_at');
            $table->enum('type', ['deadline', 'follow_up', 'review', 'custom'])->default('deadline');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_pattern')->nullable(); // daily, weekly, monthly
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_reminders');
    }
};
