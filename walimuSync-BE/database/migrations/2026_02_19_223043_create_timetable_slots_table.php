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
Schema::create('timetable_slots', function (Blueprint $table) {
    $table->id();

    $table->foreignId('school_class_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('subject_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('teacher_id')
          ->constrained('users')
          ->cascadeOnDelete();

    $table->foreignId('term_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->string('day_of_week');
    $table->time('start_time');
    $table->time('end_time');

    $table->timestamps();

    $table->unique([
    'teacher_id',
    'term_id',
    'day_of_week',
    'start_time'
], 'teacher_time_unique');
$table->unique([
    'school_class_id',
    'term_id',
    'day_of_week',
    'start_time'
], 'class_time_unique');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
