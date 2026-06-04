<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\ChatMessage;
use App\Models\Mensaje;
use App\Models\Usuario;
use App\Models\Notificacion;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\AsambleaController;
use App\Http\Controllers\PagoAtrasadoController;
use App\Events\NotificacionNueva;


Route::get('/mensajes', function () {
    return response()->json(
        Mensaje::orderBy('created_at', 'asc')->get()
    );
});

Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'correo' => 'required|email',
        'password' => 'required|string',
    ]);

    $usuario = Usuario::where('correo', $validated['correo'])
        ->where('password', $validated['password'])
        ->first();

    if (!$usuario) {
        return response()->json([
            'ok' => false,
            'error' => 'Credenciales incorrectas',
        ], 401);
    }

    return response()->json([
        'ok' => true,
        'usuario' => $usuario,
    ]);
});

Route::get('/usuarios', function () {
    return response()->json(
        Usuario::orderBy('id', 'asc')->get()
    );
});

Route::post('/mensaje', function (Request $request) {

    $msg = Mensaje::create([
        'usuario' => $request->usuario,
        'mensaje' => $request->mensaje,
    ]);

    event(new ChatMessage($msg));

    
    $usuarios = Usuario::where('correo', '!=', $request->usuario)->get();
    foreach ($usuarios as $usuario) {
        $notificacion = Notificacion::create([
            'usuario_id' => $usuario->id,
            'tipo' => 'mensaje',
            'referencia_id' => $msg->id,
            'titulo' => 'Nuevo mensaje',
            'descripcion' => 'De ' . $request->usuario . ': ' . $request->mensaje,
            'leida' => false,
        ]);

        event(new NotificacionNueva($notificacion));
    }

    return response()->json([
        'ok' => true
    ]);
});


Route::prefix('notificaciones')->group(function () {
    Route::get('/', [NotificacionController::class, 'index']);
    Route::get('/no-leidas', [NotificacionController::class, 'noLeidas']);
    Route::get('/{id}', [NotificacionController::class, 'show']);
    Route::put('/{id}/leida', [NotificacionController::class, 'marcarComoLeida']);
    Route::put('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas']);
});


Route::prefix('multas')->group(function () {
    Route::get('/{usuarioId}', [MultaController::class, 'index']);
    Route::post('/', [MultaController::class, 'store']);
    Route::get('/{id}', [MultaController::class, 'show']);
    Route::put('/{id}', [MultaController::class, 'update']);
});


Route::prefix('asambleas')->group(function () {
    Route::get('/', [AsambleaController::class, 'index']);
    Route::post('/', [AsambleaController::class, 'store']);
    Route::get('/{id}', [AsambleaController::class, 'show']);
    Route::put('/{id}', [AsambleaController::class, 'update']);
});


Route::prefix('pagos-atrasados')->group(function () {
    Route::get('/{usuarioId}', [PagoAtrasadoController::class, 'index']);
    Route::post('/', [PagoAtrasadoController::class, 'store']);
    Route::get('/{id}', [PagoAtrasadoController::class, 'show']);
});