<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemCultoAtivado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $cultoId,
        public readonly int $liturgiaId,
        public readonly int $ordem,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('culto.' . $this->cultoId);
    }

    public function broadcastAs(): string
    {
        return 'ItemCultoAtivado';
    }

    public function broadcastWith(): array
    {
        return [
            'liturgia_id' => $this->liturgiaId,
            'ordem'       => $this->ordem,
        ];
    }
}
