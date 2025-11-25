<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/u'
            ],
            'apellido' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/u'
            ],
            'email' => 'required|email|unique:users,email',
            'nombre_usuario' => [
                'required',
                'string',
                'unique:users,nombre_usuario',
                'regex:/^[A-Za-z][A-Za-z0-9_]*$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&.,\/%\^*\-\(\)

    \[\]

    ;]/'
            ],
            'rol' => 'required|in:cliente,administrador',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 422);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'nombre_usuario' => $request->nombre_usuario,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        return response()->json([
            'mensaje' => 'Registro exitoso',
            'nombre_usuario' => $user->nombre_usuario
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('nombre_usuario', $request->nombre_usuario)->first();

        if (!$user || !Hash::check($request->contrasena, $user->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $codigo = rand(100000, 999999);
        $user->codigo_verificacion = $codigo;
        $user->save();

        return response()->json([
            'mensaje' => 'Código generado',
            'usuario_id' => $user->id,
            'codigo' => $codigo // ⚠️ Solo para pruebas
        ]);
    }

    public function verificarCodigo(Request $request)
    {
        $user = User::find($request->usuario_id);

        if ($user && $user->codigo_verificacion == $request->codigo) {
            $user->codigo_verificacion = null;
            $user->save();

            Auth::login($user);
            return response()->json([
                'mensaje' => 'Inicio de sesión exitoso',
                'nombre_usuario' => $user->nombre_usuario
            ]);

        }

        return response()->json(['error' => 'Código incorrecto'], 403);
    }
}
