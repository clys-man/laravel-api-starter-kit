<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->as('auth:')->group(base_path(
    path: 'routes/auth.php',
));

Route::get('/me', fn (Request $request) => $request->user())->middleware('auth:sanctum');
