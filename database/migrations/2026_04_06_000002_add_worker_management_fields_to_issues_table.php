<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (! Schema::hasColumn('issues', 'worker_id')) {
                $table->foreignId('worker_id')->nullable()->after('user_id')->constrained('workers')->nullOnDelete();
            }

            if (! Schema::hasColumn('issues', 'deadline')) {
                $table->date('deadline')->nullable()->after('status');
            }
        });

        DB::table('issues')
            ->whereNotIn('status', ['reported', 'in_progress', 'resolved'])
            ->update(['status' => 'in_progress']);
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'worker_id')) {
                $table->dropForeign(['worker_id']);
                $table->dropColumn('worker_id');
            }

            if (Schema::hasColumn('issues', 'deadline')) {
                $table->dropColumn('deadline');
            }
        });
    }
};
