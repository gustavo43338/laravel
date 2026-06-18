<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorreoVerificado
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Debes verificar tu correo electrónico antes de continuar.',
                'requiere_verificacion' => true,
            ], 403);
        }

        return $next($request);
    }
}
