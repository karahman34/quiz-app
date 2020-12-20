<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::prefix('quizzes')->name('quizzes.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::post('/', [QuizController::class, 'store'])->name('store');

        Route::patch('/{quiz}', [QuizController::class, 'update'])->name('update');
        Route::patch('/{quiz}/right-choice', [QuizController::class, 'changeRightChoice'])->name('change_right_choice');

        Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('destroy');
    });
});
