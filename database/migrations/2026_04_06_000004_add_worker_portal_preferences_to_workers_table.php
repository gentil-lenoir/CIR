<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('availability_status', 30)->default('available')->after('status');
            $table->string('theme_preference', 30)->default('light')->after('availability_status');
            $table->string('preferred_zone')->nullable()->after('theme_preference');
            $table->string('shift_window')->nullable()->after('preferred_zone');
            $table->boolean('notify_new_assignments')->default(true)->after('shift_window');
            $table->boolean('notify_escalation_alerts')->default(true)->after('notify_new_assignments');
            $table->boolean('notify_daily_summary')->default(false)->after('notify_escalation_alerts');
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropRememberToken();
            $table->dropColumn([
                'availability_status',
                'theme_preference',
                'preferred_zone',
                'shift_window',
                'notify_new_assignments',
                'notify_escalation_alerts',
                'notify_daily_summary',
            ]);
        });
    }
};
