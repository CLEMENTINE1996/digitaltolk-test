<?php

namespace Database\Seeders;

use App\Models\Translation;
use App\Models\TranslationLanguage;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            'en' => 'English',
            'sv' => 'Swedish',
            'fr' => 'French',
            'tl' => 'Filipino',
            'ja' => 'Japanese'
        ];

        $languageModels = [];
        foreach ($languages as $code => $name) {
            $languageModels[$code] = TranslationLanguage::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_active' => true]
            );
        }

        $translations = [
            // BOTH Web & Mobile
            'nav_dashboard'               => ['text' => 'Management Dashboard', 'tags' => ['web', 'mobile']],
            'welcome_message'             => ['text' => 'Logged in as Admin', 'tags' => ['web', 'mobile']],
            'error_required_field'        => ['text' => 'This field is required.', 'tags' => ['web', 'mobile']],

            // WEB ONLY 
            'label_navigation'            => ['text' => 'Main Side Navigation', 'tags' => ['web']],
            'label_system_feedback'       => ['text' => 'Detailed System Notifications', 'tags' => ['web']],
            'label_footer_info'           => ['text' => 'All changes are logged with timestamp.', 'tags' => ['web']],

            // MOBILE ONLY 
            'label_user_details'          => ['text' => 'User Info', 'tags' => ['mobile']],
            'btn_save_changes'            => ['text' => 'Save', 'tags' => ['mobile']],
            'btn_cancel'                  => ['text' => 'Back', 'tags' => ['mobile']],
            'placeholder_enter_username'  => ['text' => 'ID', 'tags' => ['mobile']],
        ];

        foreach ($translations as $key => $config) {
            foreach ($languageModels as $code => $lang) {
                Translation::updateOrCreate(
                    ['translation_language_id' => $lang->id, 'key' => $key],
                    [
                        'content' => "[$lang->name] " . $config['text'],
                        'tags' => $config['tags']
                    ]
                );
            }
        }
    }
}
