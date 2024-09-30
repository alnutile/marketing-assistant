<?php

use Illuminate\Support\Facades\Route;

Route::post(
    '/signed', [\App\Http\Controllers\SignedUrlAuth::class, 'create']
)->name('signed_url.create');

Route::post('/webhooks/{automation:slug}', [
    \App\Http\Controllers\WebhooksController::class, 'trigger',
])
    ->name('webhooks.show');

Route::post('/testing/webhooks', function () {
    \Illuminate\Support\Facades\Log::info('Testing webhooks', [
        'payload' => request()->all(),
    ]);

    return response()->json('ok');
});
