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
        Schema::table('outlet_master', function (Blueprint $table) {
            $table->string('owner_name')->nullable();
            $table->string('outlet_mobile', 20)->nullable();
            $table->string('outlet_whatsapp', 20)->nullable();
            $table->text('outlet_address')->nullable();
            $table->text('address')->nullable();
            $table->string('gstin', 15)->nullable();
        });
    }

    public function down()
    {
        Schema::table('outlet_master', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name',
                'outlet_mobile',
                'outlet_whatsapp',
                'outlet_address',
                'address',
                'gstin'
            ]);
        });
    }
};
