<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('citizen')->after('password');
            $table->string('phone', 25)->nullable()->after('email');
            $table->unsignedBigInteger('department_id')->nullable()->after('role');

            $table->index('role');
            $table->index(['role', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['role', 'department_id']);
            $table->dropColumn('department_id');
            $table->dropColumn(['role', 'phone']);
        });
    }
};
