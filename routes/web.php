<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('projects.index');
});

Route::get('/team-invitations/{invitation}',
    [\App\Http\Controllers\TeamInviteAcceptController::class, 'accept'])
    ->middleware('signed')
    ->name('team-invitations.accept');

Route::get('/login/signed/{token}', [\App\Http\Controllers\SignedUrlAuth::class,
    'signInWithToken'])
    ->name('signed_url.login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');

    Route::controller(\App\Http\Controllers\ChatController::class)->group(
        function () {
            Route::post('/chat/{project}', 'chat')->name('chat.chat');
        }
    );

    Route::post('/daily-report/{project}', \App\Http\Controllers\DailyReportSendController::class)->name('daily-report.send');

    Route::controller(\App\Http\Controllers\TaskController::class)->group(
        function () {
            Route::get('/tasks/{project}', 'index')->name('tasks.index');
            Route::post('/tasks/{task}/complete', 'markAsComplete')->name('tasks.complete');
        }
    );

    Route::controller(\App\Http\Controllers\ProjectController::class)->group(
        function () {
            Route::get('/projects', 'index')->name('projects.index');
            Route::get('/projects/create', 'create')->name('projects.create');
            Route::post('/projects', 'store')->name('projects.store');
            Route::get('/projects/{project}', 'show')->name('projects.show');
            Route::get('/projects/{project}/edit', 'edit')->name('projects.edit');
            Route::put('/projects/{project}', 'update')->name('projects.update');
            Route::delete('/projects/{project}', 'destroy')->name('projects.destroy');
            Route::post('/projects/{project}', 'kickOff')->name('projects.kickoff');
        }
    );

});
