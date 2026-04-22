<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beat_masters', function (Blueprint $table) {
            $table->id();
            $table->string('beat_id', 50)->unique();
            $table->string('beat_name', 200);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beat_masters');
    }
};
