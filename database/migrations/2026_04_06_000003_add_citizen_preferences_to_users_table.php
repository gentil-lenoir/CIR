<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_location')->nullable()->after('phone');
            $table->string('theme_preference', 20)->default('light')->after('preferred_location');
            $table->string('text_size', 20)->default('default')->after('theme_preference');
            $table->boolean('notify_email')->default(true)->after('text_size');
            $table->boolean('notify_sms')->default(false)->after('notify_email');
            $table->boolean('community_alerts')->default(true)->after('notify_sms');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_location',
                'theme_preference',
                'text_size',
                'notify_email',
                'notify_sms',
                'community_alerts',
            ]);
        });
    }
};
