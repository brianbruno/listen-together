<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use App\ItensFila;

class MusicaFinalizada implements ShouldBroadcastNow {

    use SerializesModels;

    public $item;

    public function __construct(ItensFila $item) {
        $this->item = $item;
    }

    public function broadcastOn() {
        return new PrivateChannel('fila.default');
    }

    public function broadcastAs() {
        return 'fila.default';
    }
}