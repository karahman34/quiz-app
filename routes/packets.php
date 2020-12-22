<?php

use App\Http\Controllers\PacketController;
use Illuminate\Support\Facades\Route;

Route::prefix('packets')->name('packet.')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [PacketController::class, 'index'])->name('index');
        Route::get('/{packet}', [PacketController::class, 'show'])->name('show');
        
        Route::post('/', [PacketController::class, 'store'])->name('create');

        Route::patch('/{packet}', [PacketController::class, 'update'])->name('update');

        Route::delete('/{packet}', [PacketController::class, 'destroy'])->name('destroy');

        Route::prefix('/{packet}/sessions')->name('session.')->group(function () {
            Route::get('/', [PacketController::class, 'getSessions'])->name('get_sessions');
            Route::get('/{session}/participants', [PacketController::class, 'getSessionParticipants'])->name('get_participants');

            Route::post('/', [PacketController::class, 'createSession'])->name('create');
            Route::post('/delete', [PacketController::class, 'deleteSession'])->name('delete');
        });
    });
});
