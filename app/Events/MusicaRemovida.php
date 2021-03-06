<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MusicaRemovida implements ShouldBroadcastNow {

    use SerializesModels, InteractsWithSockets, Dispatchable;

    public $item;

    public function __construct() {
    }

    public function broadcastOn() {
        return new Channel('filas');
    }
}