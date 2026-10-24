<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetaniController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('petani', PetaniController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'api.petani.index',
        'show' => 'api.petani.show',
    ]);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/tokens/{token}', [AuthController::class, 'revoke'])
        ->whereNumber('token')
        ->name('tokens.revoke');

    Route::apiResource('petani', PetaniController::class)
        ->only(['store', 'update', 'destroy'])
        ->names([
            'store' => 'api.petani.store',
            'update' => 'api.petani.update',
            'destroy' => 'api.petani.destroy',
        ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
