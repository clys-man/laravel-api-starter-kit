<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'status' => 'online',
]));

Route::prefix('auth')->as('auth:')->group(base_path(
    path: 'routes/auth.php',
));

Route::prefix('users')->as('users:')->group(base_path(
    path: 'routes/users.php',
));

Route::get('/me', fn (Request $request) => $request->user())->middleware('auth:sanctum');
