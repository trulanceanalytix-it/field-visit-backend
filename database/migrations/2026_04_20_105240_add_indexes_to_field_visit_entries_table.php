<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_visit_entries', function (Blueprint $table) {
            $table->index('visited_at');        // used in orderBy
            $table->index('visited_date');      // used in date filter
            $table->index('distributor_id');    // used in eager load
            $table->index('beat_id');           // used in eager load
            $table->index('outlet_id');         // used in eager load
            $table->index('emp_id');            // used in filter
        });
    }

    public function down(): void
    {
        Schema::table('field_visit_entries', function (Blueprint $table) {
            $table->dropIndex(['visited_at']);
            $table->dropIndex(['visited_date']);
            $table->dropIndex(['distributor_id']);
            $table->dropIndex(['beat_id']);
            $table->dropIndex(['outlet_id']);
            $table->dropIndex(['emp_id']);
        });
    }
};
