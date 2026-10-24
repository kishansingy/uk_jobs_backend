<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Update type directly via raw SQL to bypass all casting/encryption issues
DB::table('job_sources')->where('id', 1)->update([
    'type'        => 'nhs_jobs',
    'active'      => 1,
    'authorised'  => 1,
    'updated_at'  => now(),
]);

// Set configuration separately via Eloquent so encryption works
$s = \App\Models\JobSource::find(1);
$s->configuration = [
    'keyword'            => 'nurse',
    'location'           => 'London',
    'country'            => 'GB-ENG',
    'max_pages'          => 2,
    'wipe_before_import' => true,
];
$s->saveQuietly();

$s->refresh();
echo "type        = " . $s->type->value . PHP_EOL;
echo "active      = " . ($s->active ? 'true' : 'false') . PHP_EOL;
echo "authorised  = " . ($s->authorised ? 'true' : 'false') . PHP_EOL;
echo "config      = " . json_encode($s->configuration) . PHP_EOL;
echo "Done." . PHP_EOL;
