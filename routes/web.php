<?php

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::prefix('api')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);

    Route::post('/responses', [ResponseController::class, 'store']);
});
