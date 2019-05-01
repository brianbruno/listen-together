<?php

namespace App\Jobs;

use App\Fila;
use App\ItensFila;
use App\Musica;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use SpotifyWebAPI;
use Illuminate\Support\Facades\Auth;

class CopiarPlaylists implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $user = $this->user;
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($user->spotify_token);

        $me = $api->me();

        $playlists = $api->getUserPlaylists($me->id, [
            'limit' => 50
        ]);

        foreach ($playlists->items as $playlist) {

            $fila = Fila::where('spotify_uri', $playlist->uri)->where('id_user', $user->id)->first();
            $nova = false;
            if (empty($fila)) {
                $fila = new Fila();
                $fila->id_user = $user->id;
                $fila->name = $playlist->name;
                $fila->descricao = "Playlist de ".$me->display_name;
                $fila->public = $playlist->public;
                $fila->spotify_uri = $playlist->uri;
                $fila->spotify_id = $playlist->id;
                $fila->colaborativa = $playlist->collaborative;
                $fila->capa_fila = $playlist->images[0]->url;
                $fila->capa_dinamica = false;
                $fila->save();
                $nova = true;
            }


            $playlistTracks = $api->getPlaylistTracks($playlist->id);

            foreach ($playlistTracks->items as $playlistTrack) {
                $track = $playlistTrack->track;

                $musica = Musica::where('spotify_uri', $track->uri)->first();

                if (empty($musica)) {
                    $musica = Musica::cadastrarMusica($track);
                }

                ItensFila::adicionarMusica($fila->id, $user->id, $musica->id);
            }

            $totalItens = $fila->itens()->count();

            if ($nova && $totalItens >= 3) {
                dispatch((new ProcessarFilas($fila)));
            }

        }
    }
}
