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
        Schema::create('fee_collections', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "Term 1 Exam Fee"
            $table->text('description')->nullable();
            $table->string('type'); // remedial, lunch, exam, trip, uniform, other
            $table->decimal('amount', 10, 2); // target amount per student
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete(); // null = whole school
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();

            $table->index(['status', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_collections');
    }
};
