<?php

namespace App\Models;

use App\Notifications\VerificarCorreoUsuario;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'rol',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getEmailForVerification(): string
    {
        return $this->correo;
    }

    public function routeNotificationForMail(): string
    {
        return $this->correo;
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerificarCorreoUsuario());
    }

    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class);
    }

    public function pagosAtrasados(): HasMany
    {
        return $this->hasMany(PagoAtrasado::class);
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function notificacionesNoLeidas(): int
    {
        return $this->notificaciones()->where('leida', false)->count();
    }
}
