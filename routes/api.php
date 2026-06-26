<?php

use Illuminate\Support\Facades\Route;
use App\Events\ChatMessage;
use App\Events\NotificacionNueva;
use App\Models\Mensaje;
use App\Models\Notificacion;
use App\Models\Usuario;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\AsambleaController;
use App\Http\Controllers\PagoAtrasadoController;

// ── Público ──
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// ── Autenticado (token Sanctum) ──
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/password/change', [AuthController::class, 'changePassword']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);

    // ── Requiere correo verificado ──
    Route::middleware('verified.correo')->group(function () {

        Route::get('/mensajes', function () {
            return response()->json(
                Mensaje::orderBy('created_at', 'asc')->get()
            );
        });

        Route::post('/mensaje', function (\Illuminate\Http\Request $request) {
            $usuario = $request->user();

            $validated = $request->validate([
                'mensaje' => 'required|string|max:2000',
            ]);

            $msg = Mensaje::create([
                'usuario' => $usuario->correo,
                'mensaje' => $validated['mensaje'],
            ]);

            event(new ChatMessage($msg));

            $otros = Usuario::where('id', '!=', $usuario->id)->get();
            foreach ($otros as $dest) {
                $notificacion = Notificacion::create([
                    'usuario_id' => $dest->id,
                    'tipo' => 'mensaje',
                    'referencia_id' => $msg->id,
                    'titulo' => 'Nuevo mensaje',
                    'descripcion' => 'De ' . $usuario->correo . ': ' . $validated['mensaje'],
                    'leida' => false,
                ]);
                event(new NotificacionNueva($notificacion));
            }

            return response()->json(['ok' => true]);
        });

        Route::prefix('notificaciones')->group(function () {
            Route::get('/', [NotificacionController::class, 'index']);
            Route::get('/no-leidas', [NotificacionController::class, 'noLeidas']);
            Route::put('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas']);
            Route::get('/{id}', [NotificacionController::class, 'show']);
            Route::put('/{id}/leida', [NotificacionController::class, 'marcarComoLeida']);
        });

        Route::prefix('multas')->group(function () {
            Route::get('/usuario/{usuarioId}', [MultaController::class, 'index']);
            Route::get('/detalle/{id}', [MultaController::class, 'show']);
            Route::middleware('admin')->group(function () {
                Route::post('/', [MultaController::class, 'store']);
                Route::put('/detalle/{id}', [MultaController::class, 'update']);
            });
        });

        Route::prefix('asambleas')->group(function () {
            Route::get('/', [AsambleaController::class, 'index']);
            Route::get('/{id}', [AsambleaController::class, 'show']);
            Route::middleware('admin')->group(function () {
                Route::post('/', [AsambleaController::class, 'store']);
                Route::put('/{id}', [AsambleaController::class, 'update']);
            });
        });

        Route::prefix('pagos-atrasados')->group(function () {
            Route::get('/usuario/{usuarioId}', [PagoAtrasadoController::class, 'index']);
            Route::get('/detalle/{id}', [PagoAtrasadoController::class, 'show']);
            Route::middleware('admin')->group(function () {
                Route::post('/', [PagoAtrasadoController::class, 'store']);
            });
        });

        // CRUD usuarios — solo administrador
        Route::middleware('admin')->prefix('usuarios')->group(function () {
            Route::get('/', [UsuarioController::class, 'index']);
            Route::post('/', [UsuarioController::class, 'store']);
            Route::get('/{usuario}', [UsuarioController::class, 'show']);
            Route::put('/{usuario}', [UsuarioController::class, 'update']);
            Route::delete('/{usuario}', [UsuarioController::class, 'destroy']);
            Route::post('/{usuario}/reenviar-verificacion', [UsuarioController::class, 'resendVerification']);
        });
    });
});
