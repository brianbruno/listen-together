<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\ItensFila;

class MusicaFinalizada implements ShouldBroadcastNow {

    use SerializesModels, InteractsWithSockets, Dispatchable;

    public $item;

    public function __construct(ItensFila $item) {
        $this->item = $item;
    }

    public function broadcastOn() {
        return new Channel('filas');
    }
}