<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


use App\Http\Controllers\UsuarioController;

Route::post('/registro', [UsuarioController::class, 'registrar']);
Route::post('/login', [UsuarioController::class, 'loginConCodigo']);
Route::post('/verificar-codigo', [UsuarioController::class, 'verificarCodigo']);

Route::get('/usuarios', [UsuarioController::class, 'listarUsuarios']);

Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminar']);
Route::post('/usuarios/{id}/toggle', [UsuarioController::class, 'cambiarEstado']);

// Obtener un usuario por ID
Route::get('/usuarios/{id}', [UsuarioController::class, 'mostrar']);

// Actualizar un usuario por ID
Route::put('/usuarios/{id}', [UsuarioController::class, 'actualizar']);


/* ---------------------------------------------------- */

use App\Http\Controllers\DobleFactorAuthController;

Route::apiResource('doblefactorauth', DobleFactorAuthController::class);

/* ---------------------------------------------------- */

use App\Http\Controllers\AdministradorController;

Route::apiResource('administrador', AdministradorController::class);

/* ---------------------------------------------------- */

use App\Http\Controllers\ClienteController;

Route::apiResource('cliente', ClienteController::class);

/* ---------------------------------------------------- */

use App\Http\Controllers\ProductoController;

//Route::apiResource('producto', ProductoController::class);

Route::get('/productos', [ProductoController::class, 'index']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
Route::patch('/productos/{id}/estado', [ProductoController::class, 'toggleEstado']);



/* ---------------------------------------------------- */

use App\Http\Controllers\PedidoController;

//Route::apiResource('pedido', PedidoController::class);

Route::get('/pedidos', [PedidoController::class, 'index']);       // listar todos
Route::get('/pedidos/{id}', [PedidoController::class, 'show']);   // ver uno
Route::get('/clientes/{id}/pedidos', [PedidoController::class, 'historialPorCliente']);
Route::post('/pedidos', [PedidoController::class, 'store']);      // crear
Route::put('/pedidos/{id}', [PedidoController::class, 'update']); // actualizar
Route::delete('/pedidos/{id}', [PedidoController::class, 'destroy']); // eliminar




/* ---------------------------------------------------- */

use App\Http\Controllers\DetallePedidoController;

//Route::apiResource('detallepedido', DetallePedidoController::class);

Route::get('/detallepedido', [DetallePedidoController::class, 'index']);       // listar todos
Route::get('/detallepedido/{id}', [DetallePedidoController::class, 'show']);   // ver uno
Route::post('/detallepedido', [DetallePedidoController::class, 'store']);      // crear detalle y descontar stock
Route::put('/detallepedido/{id}', [DetallePedidoController::class, 'update']); // actualizar detalle
Route::delete('/detallepedido/{id}', [DetallePedidoController::class, 'destroy']); // eliminar detalle



/* ---------------------------------------------------- */

use App\Http\Controllers\InventarioController;

//Route::apiResource('inventario', InventarioController::class);

Route::get('/inventario', [InventarioController::class, 'index']);
Route::post('/inventario', [InventarioController::class, 'store']);
Route::get('/inventario/{id}', [InventarioController::class, 'show']);
Route::put('/inventario/{id}', [InventarioController::class, 'update']);
Route::delete('/inventario/{id}', [InventarioController::class, 'destroy']);


/* ---------------------------------------------------- */

use App\Http\Controllers\MovimientoInventarioController;

//Route::apiResource('movimientoinventario', MovimientoInventarioController::class);

Route::get('/movimientos', [MovimientoInventarioController::class, 'index']);
Route::post('/movimientos', [MovimientoInventarioController::class, 'store']);
Route::get('/movimientos/{id}', [MovimientoInventarioController::class, 'show']);
Route::delete('/movimientos/{id}', [MovimientoInventarioController::class, 'destroy']);



/* ---------------------------------------------------- */

use App\Http\Controllers\QrController;

Route::get('/qr', [QrController::class, 'index']);
Route::post('/qr', [QrController::class, 'store']);
Route::get('/qr/{id}', [QrController::class, 'show']);
Route::delete('/qr/{id}', [QrController::class, 'destroy']);
Route::post('/qr/validar', [QrController::class, 'validar']);





