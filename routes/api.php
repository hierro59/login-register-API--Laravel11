<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

/**
 * PUBLIC ROUTES
 */

Route::post('login', [RegisterController::class, "login"])->name("user.login");

/**
 * AUTH ROUTES
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('register', [RegisterController::class, "register"])->name("user.register");
    Route::get('logout', [RegisterController::class, "logout"])->name("user.logout");
});
