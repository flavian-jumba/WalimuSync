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
Schema::create('substitutions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('timetable_slot_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('substitute_teacher_id')
          ->constrained('users')
          ->cascadeOnDelete();

    $table->date('date');

    $table->timestamps();
    $table->unique([
    'timetable_slot_id',
    'date'
]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitutions');
    }
};
