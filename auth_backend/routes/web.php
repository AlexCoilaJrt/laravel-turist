<?php

use App\Http\Controllers\InvitadoController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Emprendedor\ServicioController;
use App\Http\Controllers\Usuario\ReservaController;
use App\Http\Controllers\Auth\RegisterController;


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
    // Rutas accesibles solo para Invitado
    Route::middleware(['role:Invitado'])->group(function () {
    Route::get('/invitado', 'InvitadoController@index');
    Route::get('/invitado/paquetes', [InvitadoController::class, 'verPaquetes']);
    Route::get('/paquetes', [InvitadoController::class, 'verPaquetes']);
    Route::get('/catalogo', [InvitadoController::class, 'verCatalogo']);

    // Otras rutas para Invitado
    });
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});
