<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    
    public function index(Request $request)
    {
        $usuarioId = $request->input('usuario_id');
        
        if (!$usuarioId) {
            return response()->json(['error' => 'usuario_id requerido'], 400);
        }

        $notificaciones = Notificacion::where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notificacion) {
                return [
                    'id' => $notificacion->id,
                    'tipo' => $notificacion->tipo,
                    'titulo' => $notificacion->titulo,
                    'descripcion' => $notificacion->descripcion,
                    'leida' => $notificacion->leida,
                    'referencia_id' => $notificacion->referencia_id,
                    'created_at' => $notificacion->created_at,
                    'detalles' => $notificacion->getDetalles()
                ];
            });

        return response()->json($notificaciones);
    }

   
    public function noLeidas(Request $request)
    {
        $usuarioId = $request->input('usuario_id');
        
        if (!$usuarioId) {
            return response()->json(['error' => 'usuario_id requerido'], 400);
        }

        $notificaciones = Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'total' => $notificaciones->count(),
            'notificaciones' => $notificaciones
        ]);
    }

    
    public function marcarComoLeida($id)
    {
        $notificacion = Notificacion::find($id);
        
        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        $notificacion->update([
            'leida' => true,
            'fecha_lectura' => now()
        ]);

        return response()->json([
            'ok' => true,
            'notificacion' => $notificacion
        ]);
    }

    
    public function marcarTodasComoLeidas(Request $request)
    {
        $usuarioId = $request->input('usuario_id');
        
        if (!$usuarioId) {
            return response()->json(['error' => 'usuario_id requerido'], 400);
        }

        Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->update([
                'leida' => true,
                'fecha_lectura' => now()
            ]);

        return response()->json(['ok' => true]);
    }

    
    public function show($id)
    {
        $notificacion = Notificacion::find($id);
        
        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        return response()->json([
            'notificacion' => $notificacion,
            'detalles' => $notificacion->getDetalles()
        ]);
    }
}
