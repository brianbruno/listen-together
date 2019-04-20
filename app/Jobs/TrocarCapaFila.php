<?php

namespace App\Jobs;

use App\Fila;
use App\ItensFila;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SpotifyWebAPI;

class TrocarCapaFila implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fila, $musica;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Fila $fila, ItensFila $musica) {
        $this->fila = $fila;
        $this->musica = $musica;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $fila = $this->fila;
        $musica = $this->musica;

        $users = User::where('spotify_token', '<>', null)->where('spotify_status', '1')->get();

        foreach ($users as $user) {
            try {
                $api = new SpotifyWebAPI\SpotifyWebAPI();
                $api->setAccessToken($user->spotify_token);

                $track = $api->getTrack($musica->spotify_uri);
                $urlImage = $track->album->images[0]->url;

                $fila->capa_fila = $urlImage;
                $fila->save();

                break;
            } catch (\Exception $e) {
                //
            }
        }


    }
}
