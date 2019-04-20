<?php

namespace App\Jobs;

use App\Events\MusicaIniciada;
use App\Fila;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessarFilas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fila;

    public function __construct(Fila $fila) {
        $this->fila = $fila;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $fila = $this->fila;
        echo "Processando a fila ".$fila->name." \n";

        $users = User::where('spotify_token', '<>', null)->where('spotify_status', '1')->where('id_fila', '=', $fila->id)->get();
        $item = $fila->getProximaMusica();

        if ($item != null) {
            TrocarCapaFila::dispatchNow($fila, $item);
        } else {
            echo "Nao foi possivel recuperar a proxima musica";
        }

        event(new MusicaIniciada($item));

        foreach ($users as $user) {
            ProximaMusica::dispatchNow($user, $item->spotify_uri);
        }
        $item->status = "I";
        $item->save();
    }
}
