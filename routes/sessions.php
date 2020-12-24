<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('sessions')->name('session.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/join', [SessionController::class, 'join'])->name('view.join');
        Route::get('/{session:code}', [SessionController::class, 'start'])->name('start');

        Route::post('/join', [SessionController::class, 'join'])->name('join');
        Route::post('/search', [SessionController::class, 'searchPacket'])->name('search');
        Route::post('/{session:code}', [SessionController::class, 'finishSession'])->name('finish');
    });
});
