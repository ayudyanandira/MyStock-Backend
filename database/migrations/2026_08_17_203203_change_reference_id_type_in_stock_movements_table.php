<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE stock_movements ALTER COLUMN reference_id TYPE VARCHAR(255) USING reference_id::varchar;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE stock_movements ALTER COLUMN reference_id TYPE BIGINT USING reference_id::bigint;');
    }
};