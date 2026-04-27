<?php

namespace App\Services;

use App\Models\TranslationLanguage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TranslationGeneratorService
{
    protected array $languages = [
        'de' => 'German',
        'hi' => 'Hindi',
        'es' => 'Spanish',
        'zh' => 'Chinese',
        'ar' => 'Arabic',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'bn' => 'Bengali',
        'ko' => 'Korean',
        'vi' => 'Vietnamese',
        'it' => 'Italian',
        'tr' => 'Turkish',
        'pl' => 'Polish',
        'uk' => 'Ukrainian',
        'th' => 'Thai',
        'id' => 'Indonesian',
        'nl' => 'Dutch',
        'el' => 'Greek',
        'he' => 'Hebrew',
        'fa' => 'Persian',
        'ms' => 'Malay',
        'ro' => 'Romanian',
        'hu' => 'Hungarian',
        'cs' => 'Czech'
    ];

    public function generateBulk(int $rowsPerJob = 1000)
    {
        Log::info("Bulk Generator: Starting generation for {$rowsPerJob} rows.");
        $startTime = microtime(true);

        try {
            // add the remaining languages
            Log::debug("Bulk Generator: Syncing " . count($this->languages) . " languages.");
            $langModels = [];
            foreach ($this->languages as $code => $name) {
                $langModels[] = TranslationLanguage::updateOrCreate(
                    ['code' => $code],
                    ['name' => $name, 'is_active' => true]
                );
            }
            Log::info("Bulk Generator: Language sync complete.");

            // generate and chunk the data
            $rowsGenerated = 0;
            $chunkSize = 500;
            $batchCount = 0;

            while ($rowsGenerated < $rowsPerJob) {
                $batch = [];
                $batchCount++;

                for ($i = 0; $i < $chunkSize; $i++) {
                    if ($rowsGenerated >= $rowsPerJob) break;

                    $lang = $langModels[array_rand($langModels)];

                    $batch[] = [
                        'translation_language_id' => $lang->id,
                        'key'        => 'bulk_' . Str::random(8) . '_' . microtime(true),
                        'content'    => "[{$lang->name}] " . Str::random(10) . " " . Str::random(5),
                        'tags'       => json_encode(['bulk-test', 'auto-generated']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $rowsGenerated++;
                }

                Log::debug("Bulk Generator: Inserting Batch #{$batchCount} ({$chunkSize} rows). Current progress: {$rowsGenerated}/{$rowsPerJob}");

                // insert the data
                DB::beginTransaction();
                try {
                    DB::table('translations')->insert($batch);
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error("Bulk Generator: Database insertion failed on Batch #{$batchCount}: " . $e->getMessage());
                    throw $e;
                }
            }

            $totalTime = round(microtime(true) - $startTime, 2);
            Log::info("Bulk Generator: Successfully finished generating {$rowsGenerated} rows. Time taken: {$totalTime} seconds.");
        } catch (Exception $e) {
            Log::emergency("Bulk Generator: Fatal error during generation: " . $e->getMessage());
            throw $e;
        }
    }
}
