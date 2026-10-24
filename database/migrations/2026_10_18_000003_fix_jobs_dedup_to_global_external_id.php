<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicate jobs keeping only the first occurrence per external_id
        DB::statement('
            DELETE j1 FROM jobs j1
            INNER JOIN jobs j2
            WHERE j1.id > j2.id
              AND j1.external_id = j2.external_id
              AND j1.external_id IS NOT NULL
        ');

        Schema::table('jobs', function (Blueprint $table) {
            // MySQL won't drop a unique index used by a FK — drop FK first
            $table->dropForeign(['source_id']);

            // Drop the old composite unique index
            $table->dropUnique(['source_id', 'external_id']);

            // Re-add the FK (now without the unique constraint blocking it)
            $table->foreign('source_id')->references('id')->on('job_sources')->cascadeOnDelete();

            // Add global unique on external_id alone
            $table->unique('external_id', 'jobs_external_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropUnique('jobs_external_id_unique');
            $table->dropForeign(['source_id']);
            $table->unique(['source_id', 'external_id']);
            $table->foreign('source_id')->references('id')->on('job_sources')->cascadeOnDelete();
        });
    }
};
