<?php

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ResultsController;
use Illuminate\Support\Facades\Route;

Route::get('/form', fn () => view('app'));
Route::get('/results', fn () => view('app'));

Route::prefix('api')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/responses', [ResponseController::class, 'store']);
    Route::get('/results', [ResultsController::class, 'index']);
});
