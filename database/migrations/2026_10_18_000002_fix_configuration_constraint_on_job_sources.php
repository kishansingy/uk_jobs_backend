<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MariaDB enforces JSON validity on JSON columns, which blocks encrypted values.
        // Change configuration to longtext — encrypted data is a text blob, not valid JSON.
        DB::statement('ALTER TABLE job_sources MODIFY COLUMN configuration LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE job_sources MODIFY COLUMN configuration JSON NULL');
    }
};
