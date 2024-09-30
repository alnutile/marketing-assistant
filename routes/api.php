<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post(
    '/signed', [\App\Http\Controllers\SignedUrlAuth::class, 'create']
)->name('signed_url.create');

Route::get('/webhooks/{automation}', [
    \App\Http\Controllers\WebhooksController::class, 'show'
])
    ->name('webhooks.show');
