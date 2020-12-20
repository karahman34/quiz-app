<?php

use App\Http\Controllers\ChoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('choices')->name('choices.')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::post('/', [ChoiceController::class, 'store'])->name('store');

        Route::patch('/{choice}', [ChoiceController::class, 'update'])->name('update');

        Route::delete('/{choice}', [ChoiceController::class, 'destroy'])->name('destroy');
    });
});
