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
        Schema::table('distributor_master', function (Blueprint $table) {
            $table->string('cid', 20)->nullable()->after('id');

            // DO NOT add customer_name
            // distributor_name already exists and will be reused

            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->text('address3')->nullable();
            $table->string('district', 100)->nullable();

            $table->string('state_code', 10)->nullable();
            $table->string('pincode', 10)->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();

            $table->string('gstin', 20)->nullable();

            $table->boolean('is_active')->default(true);
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
