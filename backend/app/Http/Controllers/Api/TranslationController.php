<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Services\TranslationService;
use App\Http\Resources\TranslationResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Exception;

class TranslationController extends Controller
{
    /**
     * @param TranslationService $translationService
     */
    public function __construct(protected TranslationService $translationService) {}

    /**
     * @return JsonResponse
     */
    public function languages(): JsonResponse
    {
        try {
            $languages = $this->translationService->listActiveLanguages();
            return response()->json($languages);
        } catch (Exception $e) {
            Log::error("Failed to fetch languages: " . $e->getMessage());
            return response()->json(['message' => 'Error retrieving languages'], 500);
        }
    }

    /**
     * @param string $code
     * @return JsonResponse
     */
    public function exportLocale(string $code): JsonResponse
    {
        try {
            $data = $this->translationService->exportByLocale($code);
            return response()->json($data);
        } catch (Exception $e) {
            Log::error("Locale export failed for {$code}: " . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $translations = $this->translationService->listTranslations($request->all());
            return TranslationResource::collection($translations);
        } catch (Exception $e) {
            Log::error("Failed to fetch translations list: " . $e->getMessage());
            return response()->json(['message' => 'Error retrieving translations'], 500);
        }
    }

    /**
     * @param int $id
     * @return TranslationResource|JsonResponse
     */
    public function show(int $id): TranslationResource|JsonResponse
    {
        try {
            $translation = $this->translationService->getTranslation($id);
            return $translation
                ? new TranslationResource($translation)
                : response()->json(['message' => 'Not found'], 404);
        } catch (Exception $e) {
            Log::error("Error showing translation ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Error retrieving record'], 500);
        }
    }

    /**
     * @param TranslationRequest $request
     * @return TranslationResource|JsonResponse
     */
    public function store(TranslationRequest $request): TranslationResource|JsonResponse
    {
        try {
            $translation = $this->translationService->upsertTranslation($request->validated());
            return new TranslationResource($translation);
        } catch (Exception $e) {
            Log::error("Failed to store translation: " . $e->getMessage());
            return response()->json(['message' => 'Store failed'], 500);
        }
    }

    /**
     * @param TranslationRequest $request
     * @param int $id
     * @return TranslationResource|JsonResponse
     */
    public function update(TranslationRequest $request, int $id): TranslationResource|JsonResponse
    {
        try {
            $existing = $this->translationService->getTranslation($id);
            if (!$existing) {
                return response()->json(['message' => 'Not found'], 404);
            }
            $translation = $this->translationService->upsertTranslation($request->validated());
            return new TranslationResource($translation);
        } catch (Exception $e) {
            Log::error("Update failed for ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Update failed'], 500);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->translationService->removeTranslation($id);
            return $deleted
                ? response()->json(null, 204)
                : response()->json(['message' => 'Not found'], 404);
        } catch (Exception $e) {
            Log::error("Delete failed for ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Delete failed'], 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function export(): JsonResponse
    {
        try {
            return response()->json($this->translationService->exportTranslations());
        } catch (Exception $e) {
            Log::error("Export failed: " . $e->getMessage());
            return response()->json(['message' => 'Export failed'], 500);
        }
    }
}
