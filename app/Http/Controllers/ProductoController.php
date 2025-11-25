<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index() {
       return response()->json(Producto::all(), 200);
    }

    public function store(Request $request) {
      $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:100',
            'activo' => 'nullable|boolean',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $producto = new Producto();
$producto->nombre = $validated['nombre'];
$producto->descripcion = $validated['descripcion'] ?? null;
$producto->precio = $validated['precio'];
$producto->stock = $validated['stock'];
$producto->categoria = $validated['categoria'] ?? null;
$producto->activo = $validated['activo'] ?? true;

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombre = time() . '_' . $archivo->getClientOriginalName();
            $archivo->storeAs('public/productos', $nombre);
            $producto->imagen = $nombre;
        }

        $producto->save();

        return response()->json([
            'mensaje' => 'Producto creado correctamente',
            'producto' => $producto
        ], 201);


    }

    public function show($id) {
       $producto = Producto::findOrFail($id);
        return response()->json($producto);

    }

    public function update(Request $request, $id) {
       $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'categoria' => 'nullable|string|max:100',
            'activo' => 'nullable|boolean',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'

        ]);

        $producto->fill($validated);
         if ($request->hasFile('imagen')) {
        $archivo = $request->file('imagen');
        $nombre = time() . '_' . $archivo->getClientOriginalName();
        $archivo->storeAs('public/productos', $nombre);
        $producto->imagen = $nombre;
    }

    $producto->save();


        return response()->json([
            'mensaje' => 'Producto actualizado correctamente',
            'producto' => $producto
        ]);

    }

    public function destroy($id) {
        Producto::destroy($id);
        return response()->json(['mensaje' => 'Producto eliminado']);
    }
    public function toggleEstado(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->activo = $request->activo;
        $producto->save();

        return response()->json([
            'mensaje' => $producto->activo ? 'Producto activado' : 'Producto desactivado',
            'producto' => $producto
        ]);
    }

}
