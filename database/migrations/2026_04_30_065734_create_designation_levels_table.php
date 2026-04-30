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
        Schema::create('designation_levels', function (Blueprint $table) {
            $table->id();
            $table->string('designation_key')->unique();   // e.g. "TERRITORY SALES EXECUTIVE"
            $table->string('designation_label');           // display name, e.g. "Territory Sales Executive"
            $table->string('emp_level', 10);               // L1–L5
            $table->integer('sort_order');                 // for ordered dropdowns / hierarchy display
            $table->boolean('is_field_staff')->default(false); // TSE/Senior TSE = true
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation_levels');
    }
};
