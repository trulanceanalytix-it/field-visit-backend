<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE field_visit_entries ALTER COLUMN remark TYPE jsonb USING remark::jsonb");
        DB::statement("ALTER TABLE field_visit_entries ALTER COLUMN competitor_brands TYPE jsonb USING competitor_brands::jsonb");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE field_visit_entries ALTER COLUMN remark TYPE text USING remark::text");
        DB::statement("ALTER TABLE field_visit_entries ALTER COLUMN competitor_brands TYPE text USING competitor_brands::text");
    }
};
