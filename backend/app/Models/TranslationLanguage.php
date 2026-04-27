<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TranslationLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
