<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Qr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Pedido;
use Illuminate\Support\Facades\Log;




class QrController extends Controller
{
    public function index() {
        return Qr::with('pedido')->get();

    }

    public function store(Request $request) {
           Log::info('Generando QR para pedido:', $request->all());

         try {
        $pedido = Pedido::with('detalles')->findOrFail($request->idPedido);

        if ($pedido->detalles->isEmpty()) {
            return response()->json([
                'error' => 'El pedido no tiene productos. No se puede generar el QR.'
            ], 400);
        }
        if ($pedido->idQR) {
            return response()->json([
                'mensaje' => 'Este pedido ya tiene un QR generado',
                'imagen' => asset("qr/qr_{$pedido->idPedido}.svg"),
                'pedido' => $pedido
            ]);
        }


        $codigo = uniqid('QR-', true);

        $qr = Qr::create([
            'codigoQR' => $codigo,
            'fechaGeneracion' => now(),
            'activo' => true,
        ]);

        $pedido->idQR = $qr->idQR;
        $pedido->save();

        // ⚠️ Generar QR como SVG (no usa Imagick ni GD)
        $svg = QrCode::size(200)
            ->errorCorrection('H')
            ->generate("Pago pedido #{$pedido->idPedido} - Código: {$codigo}");

        $nombreArchivo = "qr_{$pedido->idPedido}.svg";
        $ruta = public_path("qr/{$nombreArchivo}");
        file_put_contents($ruta, $svg);

        return response()->json([
            'mensaje' => 'QR generado en formato SVG',
            'qr' => $qr,
            'imagen' => asset("qr/{$nombreArchivo}"),
            'pedido' => $pedido
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error al generar QR: ' . $e->getMessage());
        return response()->json([
            'error' => 'Error interno al generar QR',
            'detalle' => $e->getMessage()
        ], 500);
    }

    

    }

    public function show($id) {
        $qr = Qr::with('pedido')->findOrFail($id);
        return response()->json($qr);

    }

    public function update(Request $request, $id) {
        $qr = Qr::findOrFail($id);
        $qr->update($request->all());
        return response()->json($qr);
    }

    public function destroy($id) {
        Qr::destroy($id);
        return response()->json(['mensaje' => 'QR eliminado']);
    }
    public function validar(Request $request)
{
    $codigo = $request->codigo;

    $qr = Qr::where('codigoQR', $codigo)->first();

    if (!$qr || !$qr->activo) {
        return response()->json([
            'error' => 'QR inválido o expirado'
        ], 404);
    }

    $pedido = Pedido::where('idQR', $qr->idQR)->first();

    if (!$pedido) {
        return response()->json([
            'error' => 'QR no está vinculado a ningún pedido'
        ], 404);
    }

    return response()->json([
        'mensaje' => 'QR válido',
        'pedido' => $pedido
    ], 200);
}

}
