<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use Illuminate\Http\Request;

class AdministradorController extends Controller
{
    public function index() {
        return Administrador::all();
    }

    public function store(Request $request) {
        $admin = Administrador::create($request->all());
        return response()->json($admin, 201);
    }

    public function show($id) {
        return Administrador::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $admin = Administrador::findOrFail($id);
        $admin->update($request->all());
        return response()->json($admin);
    }

    public function destroy($id) {
        Administrador::destroy($id);
        return response()->json(['mensaje' => 'Administrador eliminado']);
    }
}
