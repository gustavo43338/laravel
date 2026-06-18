<?php

namespace App\Http\Controllers;

use App\Models\Asamblea;
use App\Models\Notificacion;
use App\Events\AsambleyaNueva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsambleaController extends Controller
{
    public function index()
    {
        $asambleas = Asamblea::orderBy('fecha', 'desc')->get();
        return response()->json($asambleas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'lugar' => 'required|string',
            'agenda' => 'nullable|string',
            'estado' => 'in:programada,en_curso,finalizada',
        ]);

        $asamblea = Asamblea::create($validated);

        $usuariosIds = DB::table('usuarios')->pluck('id');
        foreach ($usuariosIds as $usuarioId) {
            Notificacion::create([
                'usuario_id' => $usuarioId,
                'tipo' => 'asamblea',
                'referencia_id' => $asamblea->id,
                'titulo' => 'Nueva Asamblea Programada',
                'descripcion' => $asamblea->titulo . ' - ' . $asamblea->fecha->format('d/m/Y H:i'),
            ]);
        }

        event(new AsambleyaNueva($asamblea));

        return response()->json([
            'ok' => true,
            'asamblea' => $asamblea,
        ], 201);
    }

    public function show($id)
    {
        $asamblea = Asamblea::find($id);

        if (!$asamblea) {
            return response()->json(['error' => 'Asamblea no encontrada'], 404);
        }

        return response()->json($asamblea);
    }

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
            'estado' => 'in:programada,en_curso,finalizada',
        ]);

        $asamblea->update($validated);

        return response()->json([
            'ok' => true,
            'asamblea' => $asamblea,
        ]);
    }
}
