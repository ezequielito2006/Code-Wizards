<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    // Listar inventario
    public function index()
    {
        return Inventario::with('producto')->get();
    }

    // Crear inventario manualmente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fechaActualizacion' => 'required|date',
            'stockActual' => 'required|integer|min:0',
            'idProducto' => 'nullable|integer|exists:producto,idProducto',
            'activo' => 'nullable|boolean',
        ]);

        $inventario = Inventario::create($validated);

        return response()->json([
            'mensaje' => 'Inventario creado correctamente',
            'inventario' => $inventario
        ], 201);
    }

    // Mostrar inventario por ID
    public function show($id)
    {
        $inventario = Inventario::with('producto')->findOrFail($id);
        return response()->json($inventario);
    }

    // Actualizar inventario
    public function update(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);

        $validated = $request->validate([
            'fechaActualizacion' => 'nullable|date',
            'stockActual' => 'nullable|integer|min:0',
            'idProducto' => 'nullable|integer|exists:producto,idProducto',
            'activo' => 'nullable|boolean',
        ]);

        $inventario->update($validated);

        return response()->json([
            'mensaje' => 'Inventario actualizado correctamente',
            'inventario' => $inventario
        ]);
    }

    // Eliminar inventario
    public function destroy($id)
    {
        Inventario::destroy($id);
        return response()->json(['mensaje' => 'Inventario eliminado']);
    }
   public function registrarInventarioDesdeDetalle($idProducto, $cantidad, $idUsuario = null)
{
    try {
        $inventario = Inventario::where('idProducto', $idProducto)
                                ->where('activo', true)
                                ->orderByDesc('fechaActualizacion')
                                ->first();

        if (!$inventario) {
    Log::warning("No se encontró inventario para producto $idProducto. Insertando inventario inicial.");

    $producto = \App\Models\Producto::find($idProducto);

    Inventario::create([
        'idProducto' => $idProducto,
        'stockActual' => $producto->stock ?? 0,
        'fechaActualizacion' => now(),
        'activo' => true
    ]);

    $inventario = Inventario::where('idProducto', $idProducto)
                            ->where('activo', true)
                            ->orderByDesc('fechaActualizacion')
                            ->first();
}


        if ($inventario->stockActual < $cantidad) {
            Log::warning("Stock insuficiente para producto $idProducto. Disponible: {$inventario->stockActual}");
            return false;
        }

        Inventario::create([
            'idProducto' => $idProducto,
            'stockActual' => $inventario->stockActual - $cantidad,
            'fechaActualizacion' => now(),
            'activo' => true
        ]);

        MovimientoInventario::create([
            'idProducto' => $idProducto,
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'descripcion' => 'Salida por pedido',
            'fecha' => now(),
            'idUsuario' => $idUsuario ?? 1,
            'activo' => true
        ]);

        Log::info("Inventario y movimiento registrados para producto $idProducto");
        return true;

    } catch (\Exception $e) {
        Log::error("Error al registrar inventario/movimiento: " . $e->getMessage());
        return false;
    }

}

}