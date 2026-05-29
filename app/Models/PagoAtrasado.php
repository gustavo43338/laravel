<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoAtrasado extends Model
{
    protected $table = 'pagos_atrasados';

    protected $fillable = [
        'usuario_id',
        'concepto',
        'monto',
        'fecha_vencimiento',
        'dias_atraso',
        'detalles'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
