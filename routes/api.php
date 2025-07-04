<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\ManoobraController;
use App\Http\Controllers\MaquinariaController;
use App\Http\Controllers\ProyectosController;
use App\Http\Controllers\MovimientosInventarioController;

use Barryvdh\DomPDF\Facade\Pdf;
 
// Rutas públicas
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/register', [UserController::class, 'register']);

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Ruta de usuario actual
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas de recursos
    Route::apiResource('/productos', ProductoController::class);
    Route::apiResource('/categorias', CategoriaController::class);
    Route::apiResource('/marcas', MarcaController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/movimientos_inventarios', MovimientosInventarioController::class);
    Route::apiResource('/materias', MateriaController::class);
    Route::apiResource('/manoobras', ManoobraController::class);
    Route::apiResource('/maquinarias', MaquinariaController::class);
    Route::apiResource('/proyectos', ProyectosController::class);

    // Rutas de reportes
    Route::prefix('reportes')->group(function () {
        Route::get('/movimientos-diarios', [MovimientosInventarioController::class, 'reporteMovimientosDiarios']);
        Route::get('/movimientos-por-producto', [MovimientosInventarioController::class, 'reporteMovimientosPorProducto']);
        Route::get('/inventario-actual', [MovimientosInventarioController::class, 'reporteInventarioActual']);
        Route::get('/movimientos-por-producto-detallado', [MovimientosInventarioController::class, 'movimientosPorProducto']);
    });
});


