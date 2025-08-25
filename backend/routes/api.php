<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\ExamRequestController;

Route::apiResource('exams', ExamController::class);
Route::apiResource('packages', PackageController::class);

Route::prefix('exam-requests')->group(function () {
    Route::post('/', [ExamRequestController::class, 'store']);
    Route::get('/', [ExamRequestController::class, 'index']);
    Route::get('{id}', [ExamRequestController::class, 'show']);
    Route::get('{id}/print', [ExamRequestController::class, 'print']);
});
