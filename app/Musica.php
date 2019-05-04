<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI;

class Musica extends Model {

    protected $table = 'musicas';

    public static function buscarMusicasSpotify($textoBusca, $user = null) {

        $musicas = [];

        if (empty($user)) {
            $user = Auth::user();
        }

        try {
            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken($user->spotify_token);

            $results = $api->search($textoBusca, 'track');
            $musicas = $results->tracks->items;
        } catch (\Exception $e) {
            echo "Eroo! ".$e->getMessage();
        }

        return $musicas;
    }

    public static function encontrarUriMusica($uri, $user = null) {
        if (empty($user)) {
            $user = Auth::user();
        }

        $musica = Musica::where('spotify_uri', $uri)->first();

        if (empty($musica)) {
            $musica = Musica::cadastrarMusicaSpotify($uri, $user);
        }

        return $musica;
    }

    public static function cadastrarMusicaSpotify($uri, $user = null) {

        if (empty($user)) {
            $user = Auth::user();
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($user->spotify_token);
        $track = $api->getTrack($uri);

        return Musica::cadastrarMusica($track);
    }

    public static function cadastrarMusica($track) {
        $musica = new Musica();
        $musica->name = $track->artists[0]->name. " - " . $track->name;
        $musica->spotify_uri = $track->uri;
        $musica->spotify_id = $track->id;
        $musica->ms_duration = $track->duration_ms;
        $musica->save();

        return $musica;
    }

    public function likes() {
        return $this->hasMany('App\MusicaLikes', 'id_musica', 'id');
    }
}
