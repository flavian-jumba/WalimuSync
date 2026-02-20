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
Schema::create('academic_calendars', function (Blueprint $table) {
    $table->id();

    $table->date('date');
    $table->string('type'); // holiday, exam, event, remedial
    $table->string('description')->nullable();

    $table->timestamps();
    $table->index('date');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
    }
};
