<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix('activities')->name('activity.')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [ActivityController::class, 'index']);

        Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
    });
});
