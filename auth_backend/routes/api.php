<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitadoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        
        Route::middleware('auth:api')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::resource('users', UserController::class);
    });
    // Ruta para obtener los paquetes turísticos
Route::get('/invitado/paquetes', [InvitadoController::class, 'verPaquetes']);

// Ruta para obtener el catálogo de productos (artesanías y comidas)
Route::get('/invitado/catalogo', [InvitadoController::class, 'verCatalogo']);

});

Route::get('/', [AuthController::class, 'unauthorized'])->name('login');