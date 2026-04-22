<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('employee_beat_outlet_map', function (Blueprint $table) {
            $table->string('cm_id')->nullable();
            $table->string('cm_name')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('district')->nullable();
            $table->string('town_name')->nullable();
            $table->string('s_gr')->nullable();

        });
    }

    public function down()
    {
        Schema::table('employee_beat_outlet_map', function (Blueprint $table) {
            $table->dropColumn([
                'cm_id',
                'cm_name',
                'employee_name',
                'district',
                'town_name',
                's_gr',

            ]);
        });
    }
};
