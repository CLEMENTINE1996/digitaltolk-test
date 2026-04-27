<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Translation;
use App\Models\TranslationLanguage;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected TranslationLanguage $language;
    protected User $user;
    protected TranslationService $translationService;

    /**
     * Set up common data for the tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create an active language to associate with translations
        $this->language = TranslationLanguage::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->translationService = app(TranslationService::class);
    }

    /**
     * Test the API index endpoint with search filters.
     * * @return void
     */
    public function test_can_list_and_search_translations(): void
    {
        // create dummy data
        Translation::create([
            'translation_language_id' => $this->language->id,
            'key' => 'btn_save',
            'content' => 'Save Changes',
            'tags' => ['web']
        ]);

        // call the api
        $response = $this->getJson('/api/translations?key=btn_save');

        // check structure and content
        $response->assertStatus(200)
            ->assertJsonPath('data.0.key', 'btn_save')
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test the upsert (store/update) functionality.
     * * @return void
     */
    public function test_can_store_or_update_translation(): void
    {
        $payload = [
            'translation_language_id' => $this->language->id,
            'key' => 'nav_home',
            'content' => 'Home Page',
            'tags' => ['mobile']
        ];

        // test create
        $response = $this->postJson('/api/translations', $payload);
        $response->assertStatus(201);
        $this->assertDatabaseHas('translations', ['key' => 'nav_home']);

        // test update
        $payload['content'] = 'Updated Home';
        $this->postJson('/api/translations', $payload);

        $this->assertDatabaseHas('translations', [
            'key' => 'nav_home',
            'content' => 'Updated Home'
        ]);
    }

    /**
     * Test the specialized locale export functionality.
     * * @return void
     */
    public function test_can_export_specific_locale_with_tags(): void
    {
        Translation::create([
            'translation_language_id' => $this->language->id,
            'key' => 'msg_success',
            'content' => 'Saved!',
            'tags' => ['api']
        ]);

        $response = $this->getJson("/api/translations/export/{$this->language->code}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'msg_success' => ['content', 'tags']
            ])
            ->assertJsonPath('msg_success.tags.0', 'api');
    }

    /**
     * Test the delete functionality.
     * * @return void
     */
    public function test_can_delete_translation(): void
    {
        $translation = Translation::create([
            'translation_language_id' => $this->language->id,
            'key' => 'temp_key',
            'content' => 'Delete me'
        ]);

        $this->deleteJson("/api/translations/{$translation->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    /**
     * Test export performace
     * * @return void
     */

    public function test_export_by_locale_performance_threshold(): void
    {
        // create 10k temp data
        $generator = app(\App\Services\TranslationGeneratorService::class);
        $generator->generateBulk(10000);

        // measure the execution time
        $start = microtime(true);

        $this->translationService->exportByLocale('en');

        $end = microtime(true);
        $executionTime = ($end - $start) * 1000;

        // check time
        $this->assertLessThan(200, $executionTime, "Export took {$executionTime}ms, which exceeds the 200ms limit.");
    }
}
