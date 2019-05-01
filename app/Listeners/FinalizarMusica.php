<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 20/04/2019
 * Time: 13:08
 */

namespace App\Listeners;


use App\Events\MusicaFinalizada;
use App\Events\MusicaIniciada;
use App\Musica;

class FinalizarMusica {

    public function __construct() {
        //
    }

    public function handle(MusicaIniciada $event) {
        $item = $event->getItem();
        $musica = Musica::find($item->id_musica);
        $tempoTotalSegundos = $musica->ms_duration / 1000;
        \App\Jobs\FinalizarMusica::dispatch($item)->delay(now()->addSeconds($tempoTotalSegundos));
    }

}