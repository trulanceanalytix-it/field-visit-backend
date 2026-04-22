<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitor_brands', function (Blueprint $table) {
            // Drop old unique constraint on name
            $table->dropUnique('competitor_brands_name_unique');

            // Add composite unique constraint (name + category)
            $table->unique(['name', 'category'], 'competitor_brands_name_category_unique');
        });
    }

    public function down(): void
    {
        Schema::table('competitor_brands', function (Blueprint $table) {
            // Drop composite unique
            $table->dropUnique('competitor_brands_name_category_unique');

            // Restore old unique on name
            $table->unique('name', 'competitor_brands_name_unique');
        });
    }
};
