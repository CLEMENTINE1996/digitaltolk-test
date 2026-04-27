<?php

namespace App\Repositories;

use App\Models\Translation;
use App\Models\TranslationLanguage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;

class TranslationRepository
{
    /**
     * Get all active translation languages.
     * * @return Collection
     */
    public function getActiveLanguages(): Collection
    {
        return TranslationLanguage::where('is_active', true)->get();
    }

    /**
     * Get translations for a specific locale code using a high-performance join.
     * * @param string $code
     * @return BaseCollection
     */
    public function getByLocaleCode(string $code): BaseCollection
    {
        return Translation::query()
            ->join(
                'translation_languages',
                'translations.translation_language_id',
                '=',
                'translation_languages.id'
            )
            ->where('translation_languages.code', $code)
            ->select('translations.key', 'translations.content', 'translations.tags')
            ->get();
    }

    /**
     * Get translations filtered by language ID.
     * * @param array $filters
     * @return Collection
     * @throws \Exception
     */
    public function getTranslationsByLanguage(array $filters): Collection
    {
        $languageId = $filters['language_id'] ?? null;

        if (!$languageId) {
            throw new \Exception('Language ID is required for this filter.');
        }

        return Translation::query()
            ->where('translation_language_id', $languageId)
            ->with('translationLanguage')
            ->get();
    }

    /**
     * Get a paginated list of translations with optional search filters.
     * * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters): LengthAwarePaginator
    {
        return Translation::query()
            ->when($filters['tag'] ?? null, function ($query, $tag) {
                return $query->whereJsonContains('tags', $tag);
            })
            ->when($filters['key'] ?? null, function ($query, $key) {
                return $query->where('key', 'like', $key . '%');
            })
            ->when($filters['content'] ?? null, function ($query, $content) {
                return $query->where('content', 'like', '%' . $content . '%');
            })
            ->with('translationLanguage')
            ->orderByDesc('id')
            ->paginate(20);
    }

    /**
     * Find a translation record by its ID.
     * * @param int $id
     * @return Translation|null
     */
    public function findById(int $id): ?Translation
    {
        return Translation::with('translationLanguage')->find($id);
    }

    /**
     * Store or update a translation record (Upsert).
     * * @param array $data
     * @return Translation
     */
    public function store(array $data): Translation
    {
        return Translation::updateOrCreate(
            [
                'translation_language_id' => $data['translation_language_id'],
                'key' => $data['key']
            ],
            $data
        );
    }

    /**
     * Delete a translation record by ID.
     * * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $translation = Translation::find($id);

        return $translation ? (bool) $translation->delete() : false;
    }

    /**
     * Get a generator for all translations for memory-efficient exports.
     * * @return \Illuminate\Support\LazyCollection
     */
    public function cursorAll(): \Illuminate\Support\LazyCollection
    {
        return Translation::with('translationLanguage')->cursor();
    }
}
