<?php

namespace App\Events;

use App\Models\Asamblea;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AsambleyaNueva implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $asamblea;

    public function __construct(Asamblea $asamblea)
    {
        $this->asamblea = $asamblea;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('asambleas'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'asamblea-nueva';
    }
}
