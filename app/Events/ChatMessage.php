<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Mensaje;

class ChatMessage implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $usuario;
    public $mensaje;
    public $id;
    public $created_at;

    public function __construct(Mensaje $msg)
    {
        $this->usuario = $msg->usuario;
        $this->mensaje = $msg->mensaje;
        $this->id = $msg->id;
        $this->created_at = $msg->created_at;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'nuevo-mensaje';
    }
}