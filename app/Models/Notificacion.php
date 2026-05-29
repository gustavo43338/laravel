<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'referencia_id',
        'titulo',
        'descripcion',
        'leida',
        'fecha_lectura'
    ];

    protected $casts = [
        'fecha_lectura' => 'datetime'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function getDetalles()
    {
        return match($this->tipo) {
            'multa' => Multa::find($this->referencia_id),
            'asamblea' => Asamblea::find($this->referencia_id),
            'pago_atrasado' => PagoAtrasado::find($this->referencia_id),
            'mensaje' => Mensaje::find($this->referencia_id),
            default => null
        };
    }
}
