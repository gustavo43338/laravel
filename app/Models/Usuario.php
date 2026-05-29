<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'rol'
    ];

    protected $hidden = ['password'];

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

    public function notificacionesNoLeidas()
    {
        return $this->notificaciones()->where('leida', false)->count();
    }
}