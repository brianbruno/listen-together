<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\ItensFila;

class MusicaAdicionada implements ShouldBroadcast {

    use SerializesModels, InteractsWithSockets, Dispatchable;

    public $item;

    public function __construct(ItensFila $item) {
        $this->item = $item;
    }

    public function broadcastOn() {
        return new Channel('filas');
    }

    public function broadcastWith() {
        return ['musica' => $this->item];
    }
}