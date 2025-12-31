<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShortenUrlController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    // URL Shortener routes
    Route::prefix('urls')->group(function () {
        Route::post('shorten', [ShortenUrlController::class, 'shortenUrl'])->name('urls.shorten');
        Route::get('', [ShortenUrlController::class, 'getUserUrls'])->name('urls.list');
        Route::delete('{id}', [ShortenUrlController::class, 'deleteUrl'])->name('urls.delete');
    });
});
