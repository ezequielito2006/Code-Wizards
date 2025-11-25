<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\InventarioController;




class DetallePedidoController extends Controller
{
    public function index() {
       return DetallePedido::with(['pedido', 'producto'])->get();

    }

    public function store(Request $request) {
         Log::info('Recibiendo detalle:', $request->all());

        try {
            $validated = $request->validate([
                'idPedido' => 'required|integer|min:1',
                'idProducto' => 'required|exists:producto,idProducto',
                'cantidad' => 'required|integer|min:1'
            ]);

            $producto = Producto::findOrFail($validated['idProducto']);
            $subTotal = $producto->precio * $validated['cantidad'];

            $detalle = DetallePedido::create([
                'idPedido' => $validated['idPedido'],
                'idProducto' => $validated['idProducto'],
                'cantidad' => $validated['cantidad'],
                'subTotal' => $subTotal,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
           // ✅ Aquí llamás al InventarioController
$resultado = app(InventarioController::class)->registrarInventarioDesdeDetalle(
    $validated['idProducto'],
    $validated['cantidad'],
    $validated['idCliente'] ?? 1
);

// Log para confirmar si se ejecutó
Log::info('Resultado inventario/movimiento: ' . json_encode($resultado));



            return response()->json([
                'mensaje' => 'Detalle registrado correctamente',
                'detalle' => $detalle
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar detalle: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno',
                'detalle' => $e->getMessage()
            ], 500);
        }
    




    }

    public function show($id) {
         $detalle = DetallePedido::with(['pedido', 'producto'])->findOrFail($id);
        return response()->json($detalle);

    }

    public function update(Request $request, $id) {
        $detalle = DetallePedido::findOrFail($id);
        $detalle->update($request->all());
        return response()->json($detalle);
    }

    public function destroy($id) {
        DetallePedido::destroy($id);
        return response()->json(['mensaje' => 'DetallePedido eliminado']);
    }
}
