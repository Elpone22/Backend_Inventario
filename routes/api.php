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


 
//rutas para crud
Route::middleware('auth:sanctum')->group(function () {
  
});


//rutas para login
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/register', [UserController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/productos', ProductoController::class);
Route::apiResource('/categorias', CategoriaController::class);
Route::apiResource('/marcas', MarcaController::class);
Route::apiResource('/users',UserController::class);
Route::apiResource('/movimientos_inventarios', MovimientosInventarioController::class);




