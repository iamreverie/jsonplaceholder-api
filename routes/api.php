<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\JpAlbumController;
use App\Http\Controllers\Api\V1\JpCommentController;
use App\Http\Controllers\Api\V1\JpPhotoController;
use App\Http\Controllers\Api\V1\JpPostController;
use App\Http\Controllers\Api\V1\JpTodoController;
use App\Http\Controllers\Api\V1\JpUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/login',  [AuthController::class, 'login'])->name('v1.auth.login')->middleware('throttle:login');
    
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('v1.auth.logout');

        Route::apiResource('users',    JpUserController::class)->only(['index', 'show']);
        Route::apiResource('posts',    JpPostController::class)->only(['index', 'show']);
        Route::apiResource('comments', JpCommentController::class)->only(['index', 'show']);
        Route::apiResource('albums',   JpAlbumController::class)->only(['index', 'show']);
        Route::apiResource('photos',   JpPhotoController::class)->only(['index', 'show']);
        Route::apiResource('todos',    JpTodoController::class)->only(['index', 'show']);

    });

});