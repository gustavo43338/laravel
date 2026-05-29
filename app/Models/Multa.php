<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Multa extends Model
{
    protected $table = 'multas';

    protected $fillable = [
        'usuario_id',
        'descripcion',
        'monto',
        'estado',
        'detalles',
        'fecha_vencimiento'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
