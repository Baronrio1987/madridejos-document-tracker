<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, file, color, float
            $table->string('group')->default('general'); // general, notification, security, etc.
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true); // if setting can be viewed by non-admins
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};