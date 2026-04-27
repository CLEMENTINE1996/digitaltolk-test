<?php

namespace App\Jobs;

use App\Services\TranslationGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateBulkTranslationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * a job that will handle a portion of the 100k rows
     */
    public function __construct(protected int $rowsPerJob = 1000) {}

    public function handle(TranslationGeneratorService $service): void
    {
        $service->generateBulk($this->rowsPerJob);
    }
}
