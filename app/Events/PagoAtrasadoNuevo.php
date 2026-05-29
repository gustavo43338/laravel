<?php

namespace App\Events;

use App\Models\PagoAtrasado;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PagoAtrasadoNuevo implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $pagoAtrasado;
    public $usuarioId;

    public function __construct(PagoAtrasado $pagoAtrasado)
    {
        $this->pagoAtrasado = $pagoAtrasado;
        $this->usuarioId = $pagoAtrasado->usuario_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("usuario.{$this->usuarioId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'pago-atrasado-nuevo';
    }
}
