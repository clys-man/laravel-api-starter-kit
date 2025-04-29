<?php

declare(strict_types=1);

use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

Route::post('login', Auth\LoginController::class)
    ->middleware('throttle:5,1')
    ->name('login');

Route::post('register', Auth\RegisterController::class)
    ->middleware('throttle:3,1')
    ->name('register');

Route::post('logout', Auth\LogoutController::class)
    ->middleware(['auth:sanctum', 'throttle:10,1'])
    ->name('logout');
