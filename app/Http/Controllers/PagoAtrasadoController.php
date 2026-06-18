<?php

namespace App\Http\Controllers;

use App\Models\PagoAtrasado;
use App\Models\Notificacion;
use App\Events\PagoAtrasadoNuevo;
use Illuminate\Http\Request;

class PagoAtrasadoController extends Controller
{
    public function index($usuarioId)
    {
        $pagos = PagoAtrasado::where('usuario_id', $usuarioId)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json($pagos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'concepto' => 'required|string',
            'monto' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'dias_atraso' => 'required|integer|min:0',
            'detalles' => 'nullable|string',
        ]);

        $pagoAtrasado = PagoAtrasado::create($validated);

        $notificacion = Notificacion::create([
            'usuario_id' => $pagoAtrasado->usuario_id,
            'tipo' => 'pago_atrasado',
            'referencia_id' => $pagoAtrasado->id,
            'titulo' => 'Pago Atrasado',
            'descripcion' => "{$pagoAtrasado->concepto} - {$pagoAtrasado->dias_atraso} días de atraso",
        ]);

        event(new PagoAtrasadoNuevo($pagoAtrasado));

        return response()->json([
            'ok' => true,
            'pago_atrasado' => $pagoAtrasado,
            'notificacion' => $notificacion,
        ], 201);
    }

    public function show($id)
    {
        $pago = PagoAtrasado::find($id);

        if (!$pago) {
            return response()->json(['error' => 'Pago atrasado no encontrado'], 404);
        }

        return response()->json($pago);
    }
}
