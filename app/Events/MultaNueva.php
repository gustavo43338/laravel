<?php

namespace App\Events;

use App\Models\Multa;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MultaNueva implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $multa;
    public $usuarioId;

    public function __construct(Multa $multa)
    {
        $this->multa = $multa;
        $this->usuarioId = $multa->usuario_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("usuario.{$this->usuarioId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'multa-nueva';
    }
}
