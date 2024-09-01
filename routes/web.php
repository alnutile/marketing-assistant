<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('campaigns.index');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('campaigns.index');
    })->name('dashboard');

    Route::controller(\App\Http\Controllers\ChatController::class)->group(
        function () {
            Route::post('/chat/{campaign}', 'chat')->name('chat.chat');
        }
    );

    Route::post('/daily-report/{campaign}', \App\Http\Controllers\DailyReportSendController::class)->name('daily-report.send');

    Route::controller(\App\Http\Controllers\TaskController::class)->group(
        function () {
            Route::get('/tasks/{campaign}', 'index')->name('tasks.index');
            Route::post('/tasks/{task}/complete', 'markAsComplete')->name('tasks.complete');
        }
    );

    Route::controller(\App\Http\Controllers\CampaignController::class)->group(
        function () {
            Route::get('/campaigns', 'index')->name('campaigns.index');
            Route::get('/campaigns/create', 'create')->name('campaigns.create');
            Route::post('/campaigns', 'store')->name('campaigns.store');
            Route::get('/campaigns/{campaign}', 'show')->name('campaigns.show');
            Route::get('/campaigns/{campaign}/edit', 'edit')->name('campaigns.edit');
            Route::put('/campaigns/{campaign}', 'update')->name('campaigns.update');
            Route::delete('/campaigns/{campaign}', 'destroy')->name('campaigns.destroy');
            Route::post('/campaigns/{campaign}', 'kickOff')->name('campaigns.kickoff');
        }
    );

});
