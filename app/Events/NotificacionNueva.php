<?php

namespace App\Events;

use App\Models\Notificacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificacionNueva implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $notificacion;
    public $usuarioId;

    public function __construct(Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;
        $this->usuarioId = $notificacion->usuario_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("usuario.{$this->usuarioId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notificacion-nueva';
    }
}
