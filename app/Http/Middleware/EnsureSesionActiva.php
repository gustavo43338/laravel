<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSesionActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        $token = $usuario?->currentAccessToken();

        if (!$usuario || !$token) {
            return response()->json([
                'error' => 'Sesión no válida. Inicia sesión de nuevo.',
            ], 401);
        }

        return $next($request);
    }
}
