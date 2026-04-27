<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\GenerateBulkTranslationsJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('generate:translations-data {total=100000}', function () {
    $total = (int) $this->argument('total');
    $perJob = 1000; // 1k per job
    $jobsCount = ceil($total / $perJob);

    $this->info("Starting dispatch process for {$total} translations...");

    Log::info("COMMAND: translations:generate-bulk started.", [
        'total_target' => $total,
        'per_job' => $perJob,
        'expected_jobs' => $jobsCount
    ]);

    for ($i = 1; $i <= $jobsCount; $i++) {
        try {
            GenerateBulkTranslationsJob::dispatch($perJob);

            if ($i % 10 === 0 || $i === $jobsCount) {
                Log::debug("COMMAND: Dispatched job #{$i} of {$jobsCount}.");
                $this->comment("Progress: {$i}/{$jobsCount} jobs dispatched...");
            }
        } catch (\Exception $e) {
            Log::error("COMMAND: Failed to dispatch job #{$i}: " . $e->getMessage());
            $this->error("Error at job #{$i}. Check logs.");
        }
    }

    Log::info("COMMAND: translations:generate-bulk dispatching completed.");
    $this->info("Done! Run 'php artisan queue:work' to start processing.");
})->purpose('Generate bulk translations across multiple languages');
