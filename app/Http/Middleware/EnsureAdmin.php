<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->isAdmin()) {
            return response()->json([
                'error' => 'Acceso denegado. Se requiere rol administrador.',
            ], 403);
        }

        return $next($request);
    }
}
