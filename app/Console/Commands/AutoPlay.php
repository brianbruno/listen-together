<?php

namespace App\Console\Commands;

use App\Fila;
use App\Http\Controllers\AuthSpotifyController;
use App\ItensFila;
use App\Jobs\ProcessarFilas;
use App\Jobs\ProximaMusica;
use App\Jobs\TrocarCapaFila;
use App\Listeners\ProximaFila;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SpotifyWebAPI;
use App\Events\MusicaFinalizada;
use App\Events\MusicaIniciada;


class AutoPlay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:autoplay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia as músicas automaticamente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        ItensFila::where('status', 'I')->update(['status' => 'F']);

        $track = null;

        $filas = Fila::where('status', 'A')->get();

        foreach ($filas as $fila) {
            $itens = $fila->itens()->count();

            if ($itens > 2) {
                ProcessarFilas::dispatch($fila);
            } else {
                echo "A fila $fila->name não possui músicas suficientes. \n";
            }

        }

    }
}
