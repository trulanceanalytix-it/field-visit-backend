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
        Schema::create('visit_selfies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_visit_entry_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');        // storage path
            $table->string('image_url')->nullable(); // public URL (set after upload)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_selfies');
    }
};
