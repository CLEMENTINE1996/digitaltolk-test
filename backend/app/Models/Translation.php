<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_language_id',
        'key',
        'content',
        'tags'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'tags' => 'array',
    ];

    public function translationLanguage(): BelongsTo
    {
        return $this->belongsTo(TranslationLanguage::class, 'translation_language_id');
    }
}
