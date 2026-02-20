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
        Schema::table('academic_calendars', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->date('end_date')->nullable()->after('date');
            $table->boolean('is_all_day')->default(true)->after('end_date');
            $table->time('start_time')->nullable()->after('is_all_day');
            $table->time('end_time')->nullable()->after('start_time');
            $table->boolean('suppresses_notifications')->default(false)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_calendars', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'end_date',
                'is_all_day',
                'start_time',
                'end_time',
                'suppresses_notifications',
            ]);
        });
    }
};
