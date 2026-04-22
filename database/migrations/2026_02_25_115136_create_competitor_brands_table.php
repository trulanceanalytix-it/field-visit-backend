<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_brands', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();                    // Brand name, e.g. "Jockey", "Zivame"
            $table->string('category')->index();                 // "Leggings", "Innerwear", "Mens", etc.
            $table->string('sub_category')->nullable();          // Optional, e.g. "LW/Camisole"

            $table->boolean('is_active')->default(true);         // Soft toggle for admin
            $table->integer('sort_order')->default(999);
            // Add to up() in migration (or make new migration: php artisan make:migration add_added_by_to_competitor_brands)
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');         // Lower number = higher in list

            $table->timestamps();                                // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_brands');
    }
};
