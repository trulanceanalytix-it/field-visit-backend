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
        Schema::create('employee_beat_outlet_map', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id'); // TSE ID
            $table->foreignId('beat_id')->constrained('beat_master');
            $table->foreignId('outlet_id')->constrained('outlet_master');

            $table->date('assigned_from')->default(now());
            $table->date('assigned_to')->nullable();
            $table->string('status')->default('ACTIVE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_beat_outlet_map');
    }
};
