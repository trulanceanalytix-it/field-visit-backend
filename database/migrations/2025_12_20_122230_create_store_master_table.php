<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_master', function (Blueprint $table) {
            $table->bigIncrements('store_id');
            $table->string('store_name', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_master');
    }
};
