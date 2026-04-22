<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_masters', function (Blueprint $table) {
            $table->index('emp_name', 'idx_emp_name');
        });

        Schema::table('beat_master', function (Blueprint $table) {
            $table->index('beat_name', 'idx_beat_name');
        });

        Schema::table('outlet_master', function (Blueprint $table) {
            $table->index('outlet_name', 'idx_outlet_name');
        });

        Schema::table('distributor_master', function (Blueprint $table) {
            $table->index('distributor_name', 'idx_distributor_name');
        });
    }

    public function down(): void
    {
        Schema::table('employee_masters', function (Blueprint $table) {
            $table->dropIndex('idx_emp_name');
        });

        Schema::table('beat_master', function (Blueprint $table) {
            $table->dropIndex('idx_beat_name');
        });

        Schema::table('outlet_master', function (Blueprint $table) {
            $table->dropIndex('idx_outlet_name');
        });

        Schema::table('distributor_master', function (Blueprint $table) {
            $table->dropIndex('idx_distributor_name');
        });
    }
};
