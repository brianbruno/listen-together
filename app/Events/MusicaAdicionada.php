<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\ItensFila;

class MusicaAdicionada implements ShouldBroadcast {

    use SerializesModels;

    public $item;

    public function __construct(ItensFila $item) {
        $this->item = $item;
    }

    public function broadcastOn() {
//        return new PrivateChannel('fila.'.$this->item->id_fila);
        return new PrivateChannel('fila.default');
    }

    public function broadcastAs() {
        return 'fila.default';
    }
}