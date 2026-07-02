<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\PasswordResetCode;
use App\Notifications\RecoveryCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'sometimes|string|max:255',
        ]);

        $usuario = Usuario::where('correo', $validated['correo'])->first();

        if (!$usuario || !Hash::check($validated['password'], $usuario->password)) {
            return response()->json([
                'ok' => false,
                'error' => 'Credenciales incorrectas',
            ], 401);
        }

        if (!$usuario->hasVerifiedEmail()) {
            return response()->json([
                'ok' => false,
                'error' => 'Debes verificar tu correo antes de iniciar sesión. Revisa tu bandeja de entrada.',
                'requiere_verificacion' => true,
                'correo' => $usuario->correo,
            ], 403);
        }

        $deviceName = $validated['device_name'] ?? 'api-token';
        $usuario->tokens()->where('name', $deviceName)->delete();
        $token = $usuario->createToken($deviceName)->plainTextToken;

        return response()->json([
            'ok' => true,
            'token' => $token,
            'device_name' => $deviceName,
            'usuario' => $usuario,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['ok' => true]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'correo' => 'required|email',
        ]);

        $correo = $validated['correo'];

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = Carbon::now()->addMinutes(15);

        PasswordResetCode::create([
            'correo' => $correo,
            'code' => $code,
            'expires_at' => $expires,
        ]);

        $usuario = Usuario::where('correo', $correo)->first();
        if ($usuario) {
            $usuario->notify(new RecoveryCode($code, $expires));
        }

        return response()->json([
            'ok' => true,
            'mensaje' => 'Si el correo existe, se envió un código de recuperación. Revisa tu bandeja de entrada y spam. Si no llega, verifica que escribiste exactamente tu correo registrado.',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'ok' => true,
            'usuario' => $request->user(),
        ]);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $usuario = $request->user();

        if (!Hash::check($validated['current_password'], $usuario->password)) {
            return response()->json([
                'ok' => false,
                'error' => 'La contraseña actual es incorrecta.',
            ], 403);
        }

        $usuario->password = Hash::make($validated['password']);
        $usuario->save();

        $usuario->tokens()->delete();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Contraseña actualizada. Se cerraron todas las sesiones del usuario.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'correo' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $correo = $validated['correo'];

        $entry = PasswordResetCode::where('correo', $correo)
            ->where('code', $validated['code'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$entry || ($entry->expires_at && Carbon::now()->gt($entry->expires_at))) {
            return response()->json([
                'ok' => false,
                'error' => 'Código inválido o expirado.',
            ], 400);
        }

        $usuario = Usuario::where('correo', $correo)->first();
        if (!$usuario) {
            return response()->json([
                'ok' => false,
                'error' => 'Usuario no encontrado.',
            ], 404);
        }

        $usuario->password = Hash::make($validated['password']);
        $usuario->save();

        // remove all tokens and codes
        $usuario->tokens()->delete();
        PasswordResetCode::where('correo', $correo)->delete();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Contraseña restablecida correctamente. Se cerraron todas las sesiones.',
        ]);
    }

    public function resendVerification(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->hasVerifiedEmail()) {
            return response()->json([
                'ok' => false,
                'error' => 'Tu correo ya está verificado.',
            ], 400);
        }

        $usuario->sendEmailVerificationNotification();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Correo de verificación reenviado.',
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $usuario = Usuario::findOrFail($id);

        if (!hash_equals($hash, sha1($usuario->getEmailForVerification()))) {
            abort(403, 'Enlace de verificación inválido.');
        }

        if (!$usuario->hasVerifiedEmail()) {
            $usuario->markEmailAsVerified();
        }

        $frontend = config('app.frontend_url');

        return redirect($frontend . '/?correo_verificado=1');
    }
}
