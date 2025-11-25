<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioController extends Controller
{
    public function index() {
        return MovimientoInventario::with(['producto', 'usuario'])->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'idProducto' => 'required|integer|exists:producto,idProducto',
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255',
            'fecha' => 'required|date_format:Y-m-d H:i:s',
            'idUsuario' => 'required|integer|exists:usuario,idUsuario',
            'activo' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $inventario = Inventario::where('idProducto', $validated['idProducto'])->firstOrFail();

            if ($validated['tipo'] === 'salida' && $inventario->stockActual < $validated['cantidad']) {
                return response()->json(['error' => 'Stock insuficiente'], 400);
            }

            // Actualizar stock
            $inventario->stockActual += ($validated['tipo'] === 'entrada' ? $validated['cantidad'] : -$validated['cantidad']);
            $inventario->fechaActualizacion = now()->toDateString();
            $inventario->save();

            // Registrar movimiento
            $movimiento = MovimientoInventario::create([
                'idProducto' => $validated['idProducto'],
                'tipo' => $validated['tipo'],
                'cantidad' => $validated['cantidad'],
                'descripcion' => $validated['descripcion'] ?? null,
                'fecha' => $validated['fecha'],
                'idUsuario' => $validated['idUsuario'],
                'activo' => $validated['activo'] ?? true,
            ]);

            return response()->json([
                'mensaje' => 'Movimiento registrado',
                'movimiento' => $movimiento,
                'inventario' => $inventario,
            ], 201);
        });

    }

    public function show($id) {
        $movimiento = MovimientoInventario::with(['producto', 'usuario'])->findOrFail($id);
        return response()->json($movimiento);

    }

    public function update(Request $request, $id) {
        $movimiento = MovimientoInventario::findOrFail($id);
        $movimiento->update($request->all());
        return response()->json($movimiento);
    }

    public function destroy($id) {
        MovimientoInventario::destroy($id);
        return response()->json(['mensaje' => 'Movimiento de inventario eliminado']);
    }
}
