<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    Route::post('password/forgot', [AuthController::class, 'forgotPassword'])->middleware('throttle:10,30');
    Route::post('password/reset', [AuthController::class, 'resetPassword']);

    Route::middleware('jwt.auth')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store'])->middleware('role:admin');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->middleware('role:admin');
        Route::post('/posts/{post}/toggleLike', [PostController::class, 'toggleLike']);
        Route::post('/posts/{post}/comment', [PostController::class, 'comment']);

        Route::post('/post_images', [PostImageController::class, 'store']);
        Route::delete('/comments/{comment}/', [CommentController::class, 'destroy']);
        Route::patch('/comments/{comment}/', [CommentController::class, 'patch']);
        Route::get('/posts/{post}/comment', [PostController::class, 'commentList']);

        Route::get('/users/me', [UserController::class, 'me']);
        Route::patch('/user', [UserController::class, 'update']);
    });
});


Route::get('/posts/{post}/comment', [PostController::class, 'commentList']);
Route::get('/posts', [PostController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);

Route::post('/test', [TestController::class, 'test']);
