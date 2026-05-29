<?php

namespace App\Http\Controllers;

use App\Models\Multa;
use App\Models\Notificacion;
use App\Models\Usuario;
use App\Events\MultaNueva;
use Illuminate\Http\Request;

class MultaController extends Controller
{
    private function assertAdmin(Request $request): void
    {
        $adminId = $request->input('admin_id');
        if (!$adminId) {
            abort(response()->json(['error' => 'admin_id requerido'], 403));
        }

        $admin = Usuario::find($adminId);
        if (!$admin || $admin->rol !== 'admin') {
            abort(response()->json(['error' => 'Acceso denegado (solo admin)'], 403));
        }
    }

    /**
     * Listar multas de un usuario
     */
    public function index($usuarioId)
    {
        $multas = Multa::where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($multas);
    }

    /**
     * Crear una nueva multa
     */
    public function store(Request $request)
    {
        $this->assertAdmin($request);

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'descripcion' => 'required|string',
            'monto' => 'required|numeric|min:0',
            'estado' => 'in:pendiente,pagada,cancelada',
            'detalles' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date'
        ]);

        $multa = Multa::create($validated);

        // Crear notificación
        $notificacion = Notificacion::create([
            'usuario_id' => $multa->usuario_id,
            'tipo' => 'multa',
            'referencia_id' => $multa->id,
            'titulo' => 'Nueva Multa',
            'descripcion' => "Ha recibido una multa de \$" . number_format($multa->monto, 2)
        ]);

        // Disparar evento
        event(new MultaNueva($multa));

        return response()->json([
            'ok' => true,
            'multa' => $multa,
            'notificacion' => $notificacion
        ], 201);
    }

    /**
     * Obtener detalles de una multa
     */
    public function show($id)
    {
        $multa = Multa::find($id);
        
        if (!$multa) {
            return response()->json(['error' => 'Multa no encontrada'], 404);
        }

        return response()->json($multa);
    }

    /**
     * Actualizar una multa
     */
    public function update(Request $request, $id)
    {
        $multa = Multa::find($id);
        
        if (!$multa) {
            return response()->json(['error' => 'Multa no encontrada'], 404);
        }

        $validated = $request->validate([
            'descripcion' => 'string',
            'monto' => 'numeric|min:0',
            'estado' => 'in:pendiente,pagada,cancelada',
            'detalles' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date'
        ]);

        $multa->update($validated);

        return response()->json([
            'ok' => true,
            'multa' => $multa
        ]);
    }
}
