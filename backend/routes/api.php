<?php

use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\TranslationLanguage;
use App\Models\User;

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    return response()->json([
        'token' => $user->createToken('auth_token')->plainTextToken,
        'user' => $user
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/translations/export', [TranslationController::class, 'export']);
    Route::get('/translations/export/{code}', [TranslationController::class, 'exportLocale']);
    Route::get('/languages', [TranslationController::class, 'languages']);

    Route::apiResource('translations', TranslationController::class);
});
