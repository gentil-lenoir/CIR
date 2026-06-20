<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'municipal_manager']);

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropIndex('users_role_department_id_index');
                $table->dropColumn('department_id');
            }
        });

        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropIndex('issues_department_id_status_index');
                $table->dropColumn('department_id');
            }

            $table->foreignId('escalated_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('escalated_at')->nullable()->after('status');
            $table->text('citizen_feedback')->nullable()->after('address');
            $table->timestamp('feedback_submitted_at')->nullable()->after('citizen_feedback');
        });

        Schema::table('issue_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('issue_assignments', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropIndex('issue_assignments_department_id_status_index');
                $table->dropColumn('department_id');
            }

            $table->timestamp('deadline_at')->nullable()->after('assigned_at');
            $table->string('proof_path')->nullable()->after('notes');
            $table->string('proof_url')->nullable()->after('proof_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_assignments', function (Blueprint $table) {
            $table->dropColumn(['deadline_at', 'proof_path', 'proof_url']);
            $table->foreignId('department_id')->nullable()->after('issue_id')->constrained()->nullOnDelete();
            $table->index(['department_id', 'status']);
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['escalated_to']);
            $table->dropColumn(['escalated_to', 'escalated_at', 'citizen_feedback', 'feedback_submitted_at']);
            $table->foreignId('department_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->index(['department_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('role');
            $table->index(['role', 'department_id']);
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        DB::table('users')
            ->where('role', 'municipal_manager')
            ->update(['role' => 'admin']);
    }
};
