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
        Schema::table('outlet_master', function (Blueprint $table) {
            $table->renameColumn('image_1', 'visiting_card');
            $table->renameColumn('image_2', 'shop_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlet_master', function (Blueprint $table) {
            $table->renameColumn('visiting_card', 'image_1');
            $table->renameColumn('shop_image', 'image_2');
        });
    }
};
