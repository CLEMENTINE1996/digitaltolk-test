<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_language_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('content');
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->unique(['translation_language_id', 'key']);

            if (config('database.default') !== 'sqlite') {
                $table->string('tags_searchable')
                    ->virtualAs('json_unquote(json_extract(tags, "$"))')
                    ->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('translations', 'translation_language_id')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->dropForeign(['translation_language_id']);
                $table->dropColumn('translation_language_id');
            });
        }
        Schema::dropIfExists('translations');
    }
};
