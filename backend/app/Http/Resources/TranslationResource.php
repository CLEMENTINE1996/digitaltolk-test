<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'language_code' => $this->translationLanguage->code ?? null,
            'language_name' => $this->translationLanguage->name ?? null,
            'translation_language_id' => $this->translationLanguage->id ?? null,
            'key' => $this->key,
            'content' => $this->content,
            'tags' => $this->tags,
        ];
    }
}
