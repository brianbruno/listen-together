<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI;
use App\HistoricoMusicas;
use App\Musica;

class RemoverUriHistorico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:removeruri';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove uri da tabela historico';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $historicos = HistoricoMusicas::whereNull('id_musica')->get();
        $i = 0;
        $total = sizeof($historicos);

        while ($total > 0) {
            $i++;
            $users =  User::all();
            foreach ($users as $user) {
                try {

                    foreach ($historicos as $historico) {
                        $musica = Musica::where('spotify_uri', $historico->uri)->first();

                        if (empty($musica)) {
                            $api = new SpotifyWebAPI\SpotifyWebAPI();
                            $api->setAccessToken($user->spotify_token);
                            $track = $api->getTrack($historico->uri);

                            $musica = new Musica();
                            $musica->name = $track->artists[0]->name . " - " . $track->name;
                            $musica->spotify_uri = $track->uri;
                            $musica->spotify_id = $track->id;
                            $musica->ms_duration = $track->duration_ms;
                            $musica->save();
                        }

                        $historico->id_musica = $musica->id;
                        $historico->save();

                        $total--;

                    }
                } catch (\Exception $e) {
                    //
                }
            }
            if ($total > 0) {
                echo "Preciso dormir... \n";
                sleep(60*10);
            }
        }


    }
}
