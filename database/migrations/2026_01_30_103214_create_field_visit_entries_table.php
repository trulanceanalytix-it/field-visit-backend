<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_visit_entries', function (Blueprint $table) {
            $table->id();

            // Employee
            $table->string('emp_id', 50);
            $table->string('emp_name', 150);

            // Quantities
            $table->integer('leggings_qty')->nullable();
            $table->integer('non_leggings_qty')->nullable();
            $table->integer('innerwear_qty')->nullable();
            $table->integer('total_pcs')->nullable();
            $table->integer('opening_soh')->nullable();
            $table->integer('closing_soh')->nullable();

            // Remarks & Observations
            $table->text('remark')->nullable();
            $table->text('observation')->nullable();

            // Location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('address')->nullable();
            $table->integer('location_accuracy')->nullable();
            $table->timestamp('location_captured_at')->nullable();

            // Visit Date & Time
            $table->timestamp('visited_at')->nullable();
            $table->date('visited_date')->nullable();

            // Foreign Keys (Logical)
            $table->foreignId('distributor_id')->nullable()->constrained('distributor_master');
            $table->foreignId('beat_id')->nullable()->constrained('beat_master');
            $table->foreignId('outlet_id')->nullable()->constrained('outlet_master');

            // FSU & Branding
            $table->integer('fsu_type_1')->nullable();
            $table->integer('fsu_type_2')->nullable();
            $table->integer('fsu_type_3')->nullable();
            $table->string('fsu_image')->nullable();

            $table->boolean('instore_branding')->default(false);
            $table->string('branding_image')->nullable();

            $table->json('competitor_brands')->nullable();
            $table->string('other_brand_name')->nullable();

            // Store Grade
            $table->string('store_grade', 5)->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['emp_id', 'visited_date']);
            $table->index(['distributor_id', 'beat_id', 'outlet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_visit_entries');
    }
};
