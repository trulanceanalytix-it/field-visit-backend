<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_masters', function (Blueprint $table) {
            $table->id(); // BIGSERIAL

            $table->string('emp_id', 50)->unique();
            $table->string('emp_name', 150);

            $table->string('password')->nullable();

            $table->string('reporting_mgr_id', 50)->nullable();
            $table->string('reporting_mgr_name', 150)->nullable();

            $table->string('assigned_region', 150)->nullable();

            $table->string('emp_designation', 100)->nullable();
            $table->string('emp_level', 50)->nullable();

            $table->string('mgr_designation', 100)->nullable();
            $table->string('mgr_level', 50)->nullable();

            $table->boolean('is_admin')->default(false);

            $table->timestamps();

            // Optional useful indexes
            $table->index('emp_id');
            $table->index('reporting_mgr_id');
            $table->index('assigned_region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_masters');
    }
};
