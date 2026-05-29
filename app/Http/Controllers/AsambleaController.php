<?php

namespace App\Http\Controllers;

use App\Models\Asamblea;
use App\Models\Notificacion;
use App\Models\Usuario;
use App\Events\AsambleyaNueva;
use Illuminate\Http\Request;

class AsambleaController extends Controller
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
     * Listar todas las asambleas
     */
    public function index()
    {
        $asambleas = Asamblea::orderBy('fecha', 'desc')->get();
        return response()->json($asambleas);
    }

    /**
     * Crear una nueva asamblea
     */
    public function store(Request $request)
    {
        $this->assertAdmin($request);

        $validated = $request->validate([
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'lugar' => 'required|string',
            'agenda' => 'nullable|string',
            'estado' => 'in:programada,en_curso,finalizada'
        ]);

        $asamblea = Asamblea::create($validated);

        // Notificar a todos los usuarios
        $usuariosIds = \DB::table('usuarios')->pluck('id');
        foreach ($usuariosIds as $usuarioId) {
            Notificacion::create([
                'usuario_id' => $usuarioId,
                'tipo' => 'asamblea',
                'referencia_id' => $asamblea->id,
                'titulo' => 'Nueva Asamblea Programada',
                'descripcion' => $asamblea->titulo . ' - ' . $asamblea->fecha->format('d/m/Y H:i')
            ]);
        }

        // Disparar evento
        event(new AsambleyaNueva($asamblea));

        return response()->json([
            'ok' => true,
            'asamblea' => $asamblea
        ], 201);
    }

    /**
     * Obtener detalles de una asamblea
     */
    public function show($id)
    {
        $asamblea = Asamblea::find($id);
        
        if (!$asamblea) {
            return response()->json(['error' => 'Asamblea no encontrada'], 404);
        }

        return response()->json($asamblea);
    }

    /**
     * Actualizar una asamblea
     */
    public function update(Request $request, $id)
    {
        $asamblea = Asamblea::find($id);
        
        if (!$asamblea) {
            return response()->json(['error' => 'Asamblea no encontrada'], 404);
        }

        $validated = $request->validate([
            'titulo' => 'string',
            'descripcion' => 'string',
            'fecha' => 'date',
            'lugar' => 'string',
            'agenda' => 'nullable|string',
            'estado' => 'in:programada,en_curso,finalizada'
        ]);

        $asamblea->update($validated);

        return response()->json([
            'ok' => true,
            'asamblea' => $asamblea
        ]);
    }
}
