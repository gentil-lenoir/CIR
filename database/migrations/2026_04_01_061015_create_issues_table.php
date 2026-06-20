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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('category', 100)->nullable();
            $table->string('status', 30)->default('reported');
            $table->string('priority', 20)->default('medium');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('address')->nullable();
            $table->unsignedInteger('upvotes_count')->default(0);
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['priority', 'status']);
            $table->index(['latitude', 'longitude']);
            $table->index(['department_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
