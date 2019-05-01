<?php

namespace App\Console\Commands;

use App\Http\Controllers\Integration\VagalumeController;
use App\ItensFila;
use App\Musica;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BuscarMusicasTweets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:buscarmusicastweets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const HASH_TWEET = '#ouvirjuntos';
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
        
        $users = User::all();

        foreach ($users as $user) {
            $fileName = 'tweets'.$user->id.'.txt';
            $exists = Storage::disk('local')->exists($fileName);
            $idFila = 1;

            if ($exists) {
                $contents = Storage::get($fileName);
                $tweets = explode('([EOT])', $contents);

                foreach ($tweets as $tweet) {
                    $texto = str_replace(self::HASH_TWEET, '', $tweet);
                    $texto = preg_replace( "/\r|\n/", "", $texto);
                    $this->line("Analisando o tweet: ".$texto);
                    $results = VagalumeController::buscarTrecho($texto);

                    foreach($results as $result) {
                        $nomeArtista = $result->band;
                        $nomeMusica = $result->title;
                        echo $nomeArtista.' - '.$nomeMusica.PHP_EOL;

                        $afinidadeArtista = $user->afinidadeArtista($nomeArtista);
                        $afinidadeMusica = $user->afinidadeMusica($nomeMusica);

                        if (($afinidadeArtista + $afinidadeMusica) > 5 and ($afinidadeArtista > 15 or $afinidadeMusica > 10)) {
                            $musicasSpotify = Musica::buscarMusicasSpotify($nomeArtista.' '.$nomeMusica, $user);

                            foreach ($musicasSpotify as $musica) {
                                $contemArtistaPrincipal = false;
                                $contemNomeMusica = false;

                                foreach ($musica->artists as $artist) {
                                    if ($artist->name == $nomeArtista) {
                                        $contemArtistaPrincipal = true;
                                    }
                                }

                                if (strpos($musica->name, $nomeMusica) != false) {
                                    $contemNomeMusica = true;
                                }

                                if ($contemArtistaPrincipal or $contemNomeMusica) {
                                    $musicaSistema = Musica::encontrarUriMusica($musica->uri);
                                    ItensFila::adicionarMusica($idFila, $user->id, $musicaSistema->id);
                                    $this->line('Música adicionada!');
                                    break;
                                } else {
                                    $this->line('Nao foi a musica procurada');
                                }
                            }

                            if (sizeof($musicasSpotify) == 0) {
                                $this->line('Musica nao encontrada no spotify');
                            }

                        } else {
                            $this->line("Cliente nao possui afinidade com essa musica. AA: $afinidadeArtista AM: $afinidadeMusica");
                        }
                    }
                }
            }
        }
        
        
        return;   
    }
}
