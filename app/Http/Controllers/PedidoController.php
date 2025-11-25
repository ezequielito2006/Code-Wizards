<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index() {
        return Pedido::with(['cliente', 'qr', 'detalles'])->get();

    }

    public function store(Request $request) {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|string|max:50',
            'total' => 'required|numeric|min:0',
            'idCliente' => 'nullable|integer',
            'idQR' => 'nullable|integer|exists:qr,idQR',
            'activo' => 'nullable|boolean',
        ]);
        $validated['activo'] = true;


        $pedido = Pedido::create($validated);

        return response()->json([
            'mensaje' => 'Pedido creado correctamente',
             'idPedido' => $pedido->idPedido,
            'pedido' => $pedido
        ], 201);

    }

    public function show($id) {
       $pedido = Pedido::with(['cliente', 'qr', 'detalles'])->findOrFail($id);
        return response()->json($pedido);

    }

    public function update(Request $request, $id) {
         $pedido = Pedido::findOrFail($id);

        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'estado' => 'nullable|string|max:50',
            'total' => 'nullable|numeric|min:0',
            'idCliente' => 'nullable|integer|exists:usuario,idUsuario',
            'idQR' => 'nullable|integer|exists:qr,idQR',
            'activo' => 'nullable|boolean',
        ]);

        $pedido->update($validated);

        return response()->json([
            'mensaje' => 'Pedido actualizado correctamente',
            'pedido' => $pedido
        ]);

    }

    public function destroy($id) {
        Pedido::destroy($id);
        return response()->json(['mensaje' => 'Pedido eliminado']);
    }
    public function historialPorCliente($idCliente)
{
    // Traer pedidos con detalles y productos
    $pedidos = Pedido::with(['detalles.producto', 'qr'])
        ->where('idCliente', $idCliente)
        ->orderBy('fecha', 'desc')
        ->get();

    if ($pedidos->isEmpty()) {
        return response()->json([
            'mensaje' => 'Este cliente no tiene pedidos registrados'
        ], 404);
    }

    return response()->json($pedidos);
}
}
