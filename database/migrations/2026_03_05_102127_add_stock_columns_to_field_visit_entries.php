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
        Schema::table('field_visit_entries', function (Blueprint $table) {
            $table->integer('stock_leggings')->default(0);
            $table->integer('stock_non_leggings')->default(0);
            $table->integer('stock_innerwear')->default(0);
            $table->integer('stock_total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visit_entries', function (Blueprint $table) {
            //
        });
    }
};
