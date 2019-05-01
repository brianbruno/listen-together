<?php

namespace App\Console\Commands;

use App\Http\Controllers\Integration\TwitterController;
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
            $logs = [];
            $fileName = 'tweets'.$user->id.'.txt';
            $exists = Storage::disk('local')->exists($fileName);
            $idFila = 55;

            try {
                if ($exists) {
                    $contents = Storage::get($fileName);
                    $tweets = explode('([EOT])', $contents);

                    foreach ($tweets as $tweet) {
                        $texto = TwitterController::getOnlyTextTweet($tweet);
                        $this->salvarLog("Analisando o tweet: " . $texto, $logs);
                        $results = VagalumeController::buscarTrecho($texto);

                        foreach ($results as $result) {
                            $nomeArtista = $result->band;
                            $nomeMusica = $result->title;
                            $this->salvarLog("$nomeArtista - $nomeMusica", $logs, false);

                            $afinidadeArtista = $user->afinidadeArtista($nomeArtista);
                            $afinidadeMusica = $user->afinidadeMusica($nomeMusica);

                            if (($afinidadeArtista + $afinidadeMusica) > 5 and ($afinidadeArtista > 10 or $afinidadeMusica > 5)) {
                                $musicasSpotify = Musica::buscarMusicasSpotify($nomeArtista . ' ' . $nomeMusica, $user);

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
                                        $musicaSistema = Musica::encontrarUriMusica($musica->uri, $user);
                                        ItensFila::adicionarMusica($idFila, $user->id, $musicaSistema->id);
                                        $this->salvarLog('Música adicionada! '.$musicaSistema->name, $logs, true);
                                        break;
                                    } else {
                                        $this->salvarLog("$nomeArtista - $nomeMusica", $logs, true);
                                        $this->salvarLog('Nao foi a musica procurada', $logs, true);
                                    }
                                }

                                if (sizeof($musicasSpotify) == 0) {
                                    $this->salvarLog("$nomeArtista - $nomeMusica", $logs, true);
                                    $this->salvarLog('Musica nao encontrada no spotify', $logs, true);
                                }

                            } else {
                                $this->salvarLog("Cliente nao possui afinidade com essa musica. AA: $afinidadeArtista AM: $afinidadeMusica", $logs, false);
                            }
                        }

                        $this->salvarLog("-------------------------------------------------------------------------", $logs, false);
                    }
                }
            } catch (\Exception $e) {
                $this->salvarLog("Erro inesperado. ".$e->getMessage(), $logs, true);
            }

            $this->salvarLogArquivo($fileName, $logs);
        }
        
        return;   
    }

    private function salvarLog($texto, &$log, $exibir = true) {
        if ($exibir) $this->line($texto);

        $log[] = $texto;
    }

    private function salvarLogArquivo($userFileName, $logs) {
        if (sizeof($logs) > 0) {
            Storage::disk('local')->put("log_".$userFileName, implode(PHP_EOL, $logs));
        }
    }
}
