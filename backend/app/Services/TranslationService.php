<?php

namespace App\Services;

use App\Repositories\TranslationRepository;
use App\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationService
{
    /**
     * @param TranslationRepository $translationRepository
     */
    public function __construct(protected TranslationRepository $translationRepository) {}

    /**
     * @return Collection
     */
    public function listActiveLanguages(): Collection
    {
        return $this->translationRepository->getActiveLanguages();
    }

    /**
     * @param string $code
     * @return array
     */
    public function exportByLocale(string $code): array
    {
        $translations = $this->translationRepository->getByLocaleCode($code);

        $result = [];
        foreach ($translations as $item) {
            $result[$item->key] = [
                'content' => $item->content,
                'tags'    => is_string($item->tags) ? json_decode($item->tags, true) : $item->tags
            ];
        }

        return $result;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator|Collection
     */
    public function listTranslations(array $filters): LengthAwarePaginator|Collection
    {
        $languageId = $filters['language_id'] ?? null;
        if ($languageId) {
            return $this->translationRepository->getTranslationsByLanguage($filters);
        }
        return $this->translationRepository->getAll($filters);
    }

    /**
     * @param int $id
     * @return Translation|null
     */
    public function getTranslation(int $id): ?Translation
    {
        return $this->translationRepository->findById($id);
    }

    /**
     * @param array $data
     * @return Translation
     */
    public function upsertTranslation(array $data): Translation
    {
        return $this->translationRepository->store($data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removeTranslation(int $id): bool
    {
        return $this->translationRepository->delete($id);
    }

    /**
     * @return array
     */
    public function exportTranslations(): array
    {
        $data = [];
        foreach ($this->translationRepository->cursorAll() as $translation) {
            $code = $translation->translationLanguage->code ?? 'unknown';
            $data[$code][$translation->key] = $translation->content;
        }
        return $data;
    }
}
