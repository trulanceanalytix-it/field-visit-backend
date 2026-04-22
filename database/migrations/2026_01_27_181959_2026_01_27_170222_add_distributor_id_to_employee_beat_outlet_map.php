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
        Schema::table('employee_beat_outlet_map', function (Blueprint $table) {

            $table->foreignId('distributor_id')
                ->after('employee_id')
                ->constrained('distributor_master');

            // Strongly recommended unique constraint
            $table->unique([
                'employee_id',
                'distributor_id',
                'beat_id',
                'outlet_id'
            ], 'emp_dist_beat_outlet_unique');
        });
    }

    public function down(): void
    {
        Schema::table('employee_beat_outlet_map', function (Blueprint $table) {
            $table->dropUnique('emp_dist_beat_outlet_unique');
            $table->dropConstrainedForeignId('distributor_id');
        });
    }
};
