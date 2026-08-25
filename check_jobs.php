<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = \Illuminate\Support\Facades\DB::table('jobs')->get();
echo "Total jobs in queue: " . $jobs->count() . "\n";
foreach ($jobs as $job) {
    $payload = json_decode($job->payload);
    echo "Job ID: {$job->id}, Name: {$payload->displayName}, Attempts: {$job->attempts}\n";
}
