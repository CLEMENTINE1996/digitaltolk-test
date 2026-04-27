<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyTranslationsTableForSqliteTesting extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('translations', function (Blueprint $table) {
                $table->dropIndex(['tags_searchable']);
                $table->dropColumn('tags_searchable');
            });
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (config('database.default') !== 'sqlite') {
            Schema::table('translations', function (Blueprint $table) {
                $table->string('tags_searchable')
                    ->virtualAs('json_unquote(json_extract(tags, "$"))')
                    ->index();
            });
        }
    }
}
