<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_sources', function (Blueprint $table) {
            // How often (in minutes) to poll this source. NULL = use global default (every run).
            $table->unsignedSmallInteger('fetch_interval_minutes')->default(5)->after('fetch_method');
        });
    }

    public function down(): void
    {
        Schema::table('job_sources', function (Blueprint $table) {
            $table->dropColumn('fetch_interval_minutes');
        });
    }
};
