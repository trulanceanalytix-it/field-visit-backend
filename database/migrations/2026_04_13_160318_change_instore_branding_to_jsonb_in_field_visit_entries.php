<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Step 1: Drop default
        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding DROP DEFAULT');

        // ✅ Step 2: Drop NOT NULL constraint
        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding DROP NOT NULL');

        // ✅ Step 3: Update existing false/true values to null (boolean → jsonb safe)
        DB::statement('UPDATE field_visit_entries 
            SET instore_branding = NULL');

        // ✅ Step 4: Now change type to jsonb
        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding TYPE jsonb 
            USING instore_branding::text::jsonb');

        // ✅ Step 5: Set default as null
        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding SET DEFAULT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding DROP DEFAULT');

        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding TYPE boolean 
            USING NULL');

        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding SET DEFAULT false');

        DB::statement('ALTER TABLE field_visit_entries 
            ALTER COLUMN instore_branding SET NOT NULL');
    }
};
