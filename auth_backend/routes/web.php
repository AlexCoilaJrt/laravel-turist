<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Emprendedor\ServicioController;
use App\Http\Controllers\Usuario\ReservaController;

// Ruta por defecto para usuarios no autenticados
Route::get('/', function () {
    return response()->json(['error' => 'Inicie Sesión'], Response::HTTP_UNAUTHORIZED);
})->name('login');

// Agrupar rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {

    // Rutas para ADMIN
    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Aquí puedes añadir más rutas de administración si es necesario
    });

    // Rutas para EMPRENDEDOR
    Route::middleware('role:Emprendedor')->group(function () {
        Route::resource('/emprendedor/servicios', ServicioController::class);
        // Puedes agregar otras rutas como /emprendedor/perfil, etc.
    });

    // Rutas para USUARIO
    Route::middleware('role:Usuario')->group(function () {
        Route::resource('/usuario/reservas', ReservaController::class);
        // Otras rutas específicas del usuario como /usuario/perfil, etc.
    });
});
