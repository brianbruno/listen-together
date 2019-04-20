<?php

namespace App\Jobs;

use App\Events\MusicaFinalizada;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FinalizarMusica implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $item;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item) {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $item = $this->item;
        $item->status = "F";
        $item->save();
        event(new MusicaFinalizada($item));
        echo "Musica finalizada! ".$item->name."\n";
    }
}
