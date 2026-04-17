<?php

use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::prefix('api')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
});
