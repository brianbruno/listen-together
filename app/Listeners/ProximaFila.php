<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 20/04/2019
 * Time: 13:02
 */

namespace App\Listeners;


use App\Events\MusicaIniciada;
use App\Fila;
use App\Jobs\ProcessarFilas;

class ProximaFila {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    public function handle(MusicaIniciada $event) {
        $item = $event->getItem();
        $fila = Fila::find($item->id_fila);
        $tempoTotalSegundos = $item->ms_duration / 1000;
        echo "Enfileirando a próxima fila.\n";
        ProcessarFilas::dispatch($fila)->delay(now()->addSeconds($tempoTotalSegundos));
    }

}