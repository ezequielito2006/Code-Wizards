<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\DobleFactorAuth;

class UsuarioController extends Controller
{
    // Registro
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/u',
            'email' => 'required|email|unique:usuario,email', // ✅ singular
            'nombre_usuario' => 'required|string|unique:usuario,nombre_usuario|regex:/^[A-Za-z][A-Za-z0-9_]*$/',
            'password' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&.,\/%\^*\-\(\)

\[\]

;]/',
            'rol' => 'required|in:cliente,administrador',
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 422);
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'nombre_usuario' => $request->nombre_usuario,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'activo' => true,
        ]);

        return response()->json([
            'mensaje' => 'Registro exitoso',
            'nombre_usuario' => $usuario->nombre_usuario,
            'rol' => $usuario->rol
        ], 201);

    }

    // Login con generación de código 2FA
    public function loginConCodigo(Request $request)
    {
        $usuario = Usuario::where('nombre_usuario', $request->nombre_usuario)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        if (!$usuario->activo) {
            return response()->json(['error' => 'Usuario inactivo'], 403);
        }

        $codigo = rand(100000, 999999);

        DobleFactorAuth::create([
            'codigo' => (string)$codigo,
            'fechaEnvio' => now(),
            'estado' => true,
            'idUsuario' => $usuario->idUsuario,
            'activo' => true,
        ]);

        return response()->json([
            'usuario_id' => $usuario->idUsuario,
            'codigo' => $codigo
        ]);
    }

    // Verificación del código 2FA
    public function verificarCodigo(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'codigo' => 'required|string|size:6'
        ]);

        $usuarioId = (int) $request->usuario_id;
        $codigoIngresado = trim((string) $request->codigo);

        $registro = DobleFactorAuth::where('idUsuario', $usuarioId)
            ->where('estado', true)
            ->orderBy('idAuth', 'desc')
            ->first();

        if (!$registro) {
            return response()->json(['error' => 'No hay código activo para verificar'], 400);
        }

        if ((string) $registro->codigo !== $codigoIngresado) {
            return response()->json(['error' => 'Código inválido'], 400);
        }

        // Marcar como usado
        $registro->estado = false;
        $registro->save();

        // ✅ Buscar el usuario y devolver su nombre
        $usuario = Usuario::find($usuarioId);

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso',
            'nombre_usuario' => $usuario ? $usuario->nombre_usuario : null,
            'rol' => $usuario->rol
        ]);
    }

    public function listarUsuarios()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
    }

    public function eliminar($id)
    {
        Usuario::destroy($id);
        return response()->json(['mensaje' => 'Usuario eliminado']);
    }

    public function cambiarEstado($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return response()->json(['mensaje' => 'Estado actualizado']);
    }

    public function mostrar($id)
    {
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario);
    }

    public function actualizar(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $usuario->nombre = $request->nombre;
        $usuario->apellido = $request->apellido;
        $usuario->email = $request->email;
        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->rol = $request->rol;

        $usuario->save();

        return response()->json(['mensaje' => 'Usuario actualizado correctamente']);
    }
}
