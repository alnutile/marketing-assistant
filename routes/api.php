<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post(
    '/signed', [\App\Http\Controllers\SignedUrlAuth::class, 'create']
)->name('signed_url.create');
