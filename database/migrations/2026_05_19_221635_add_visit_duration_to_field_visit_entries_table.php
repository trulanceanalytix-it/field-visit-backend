<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_visit_entries', function (Blueprint $table) {
            $table->timestamp('check_in_time')->nullable()->after('visited_at');
            $table->timestamp('check_out_time')->nullable()->after('check_in_time');
            $table->unsignedInteger('visit_duration_seconds')->nullable()->after('check_out_time')
                  ->comment('Time spent at outlet in seconds');
        });
    }

    public function down(): void
    {
        Schema::table('field_visit_entries', function (Blueprint $table) {
            $table->dropColumn(['check_in_time', 'check_out_time', 'visit_duration_seconds']);
        });
    }
};