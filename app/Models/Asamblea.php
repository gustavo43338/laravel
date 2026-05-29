<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asamblea extends Model
{
    protected $table = 'asambleas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha',
        'lugar',
        'agenda',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function asistentes(): HasMany
    {
        return $this->hasMany(AsamblearAsistente::class);
    }
}
