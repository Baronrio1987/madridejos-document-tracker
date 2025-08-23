<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->unique()->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->enum('role', ['admin', 'department_head', 'encoder', 'viewer'])->default('viewer');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'employee_id', 'department_id', 'role', 'position', 
                'phone', 'is_active', 'last_login_at'
            ]);
        });
    }
};
