<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\ChatMessage;
use App\Models\Mensaje;

Route::get('/mensajes', function () {
    return response()->json(
        Mensaje::orderBy('created_at', 'asc')->get()
    );
});

Route::post('/mensaje', function (Request $request) {

    event(new ChatMessage(
        $request->usuario,
        $request->mensaje
    ));

    return response()->json([
        'ok' => true
    ]);
});