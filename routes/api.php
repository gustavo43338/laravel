<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\ChatMessage;

Route::post('/mensaje', function (Request $request) {

    event(new ChatMessage($request->mensaje));

    return response()->json([
        'ok' => true
    ]);
});