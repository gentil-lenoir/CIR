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
        Schema::create('issue_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('assigned');
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['worker_id', 'status']);
            $table->index(['department_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_assignments');
    }
};
