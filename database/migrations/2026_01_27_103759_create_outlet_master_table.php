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
        Schema::create('outlet_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beat_id')->constrained('beat_master');
            $table->string('outlet_name');

            // Existing outlet fields
            $table->integer('floors')->nullable();
            $table->decimal('total_sft', 10, 2)->nullable();
            $table->string('nearby_shops')->nullable();
            $table->string('signage_image')->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();

            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_master');
    }
};
