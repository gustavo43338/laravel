<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('id', 'asc')->get();

        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'rol' => ['required', Rule::in(['usuario', 'admin'])],
        ]);

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'correo' => $validated['correo'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'],
        ]);

        $usuario->sendEmailVerificationNotification();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Usuario creado. Se envió correo de verificación.',
            'usuario' => $usuario,
        ], 201);
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'correo' => ['sometimes', 'email', Rule::unique('usuarios', 'correo')->ignore($usuario->id)],
            'password' => 'sometimes|string|min:6',
            'rol' => ['sometimes', Rule::in(['usuario', 'admin'])],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $correoCambio = isset($validated['correo']) && $validated['correo'] !== $usuario->correo;

        $usuario->update($validated);

        if ($correoCambio) {
            $usuario->email_verified_at = null;
            $usuario->save();
            $usuario->sendEmailVerificationNotification();
        }

        return response()->json([
            'ok' => true,
            'usuario' => $usuario->fresh(),
            'mensaje' => $correoCambio
                ? 'Usuario actualizado. Se reenvió verificación al nuevo correo.'
                : 'Usuario actualizado.',
        ]);
    }

    public function destroy(Request $request, Usuario $usuario)
    {
        if ($request->user()->id === $usuario->id) {
            return response()->json([
                'error' => 'No puedes eliminar tu propia cuenta.',
            ], 403);
        }

        $usuario->tokens()->delete();
        $usuario->delete();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Usuario eliminado.',
        ]);
    }

    public function resendVerification(Usuario $usuario)
    {
        if ($usuario->hasVerifiedEmail()) {
            return response()->json([
                'ok' => false,
                'error' => 'El correo de este usuario ya está verificado.',
            ], 400);
        }

        $usuario->sendEmailVerificationNotification();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Correo de verificación reenviado a ' . $usuario->correo,
        ]);
    }
}
