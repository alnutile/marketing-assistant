<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::controller(\App\Http\Controllers\CampaignController::class)->group(
        function () {
            Route::get('/campaigns', 'index')->name('campaigns.index');
            Route::get('/campaigns/create', 'create')->name('campaigns.create');
            Route::post('/campaigns', 'store')->name('campaigns.store');
            Route::get('/campaigns/{campaign}', 'show')->name('campaigns.show');
            Route::get('/campaigns/{campaign}/edit', 'show')->name('campaigns.edit');
            Route::put('/campaigns/{campaign}', 'update')->name('campaigns.update');
            Route::delete('/campaigns/{campaign}', 'destroy')->name('campaigns.destroy');
        }
    );
});
