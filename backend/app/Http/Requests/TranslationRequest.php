<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'translation_language_id' => 'required|exists:translation_languages,id',
            'key'         => 'required|string|max:255',
            'content'     => 'required|string',
            'tags'        => 'nullable|array',
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json(['message' => 'Unauthorized'], 403)
        );
    }
}
