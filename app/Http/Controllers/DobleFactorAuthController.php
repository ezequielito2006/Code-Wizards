<?php

namespace App\Http\Controllers;

use App\Models\DobleFactorAuth;
use Illuminate\Http\Request;

class DobleFactorAuthController extends Controller
{
    public function index() {
        return DobleFactorAuth::all();
    }

    public function store(Request $request) {
        $auth = DobleFactorAuth::create($request->all());
        return response()->json($auth, 201);
    }

    public function show($id) {
        return DobleFactorAuth::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $auth = DobleFactorAuth::findOrFail($id);
        $auth->update($request->all());
        return response()->json($auth);
    }

    public function destroy($id) {
        DobleFactorAuth::destroy($id);
        return response()->json(['mensaje' => 'Registro eliminado']);
    }
}
