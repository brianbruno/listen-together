<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 10:51
 */

namespace App\Http\Controllers;

use App\Events\MusicaAdicionada;
use App\Fila;
use App\ItensFila;
use App\User;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI;

class FilaController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function buscarMusicas(\Illuminate\Http\Request $request) {

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken(Auth::user()->spotify_token);

        $results = $api->search($request->musica, 'track');

        $musicas = $results->tracks->items;

        $currentTrack = $api->getMyCurrentTrack();
        if ($currentTrack != null) {
            $currentTrack = $currentTrack->item->name. " - " . $currentTrack->item->artists[0]->name;
        } else {
            $currentTrack = "";
        }


        $fila = Fila::where('name', '=', 'default')->first();

        if ($fila != null) {
            $itensFila = $fila->itens()->limit(15)->get();
        } else {
            $itensFila = [];
        }

        return view('app.buscarmusicas', ['retornoBusca' => true, 'musicas' => $musicas, 'track' => $currentTrack, 'itensFila' => $itensFila]);

    }

    public function adicionarMusica($trackid, $fila) {

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken(Auth::user()->spotify_token);
        $track = $api->getTrack($trackid);

        $fila = Fila::first();

        if ($fila == null) {
            throw new \Exception("Nenhuma fila cadastrada");
        }

        $itemFila = new ItensFila();
        $itemFila->id_fila = $fila->id;
        $itemFila->id_user = Auth::user()->id;
        $itemFila->name = $track->artists[0]->name. " - " . $track->name;
        $itemFila->spotify_uri = $track->uri;
        $itemFila->ms_duration = $track->duration_ms;
        $itemFila->save();

        event(new MusicaAdicionada($itemFila));

        return redirect('/');

    }

    public function executarMusica($id) {

        $item = ItensFila::find($id);

        $users = User::where('spotify_token', '<>', null)->get();

        foreach ($users as $user) {
            try {
                $api = new SpotifyWebAPI\SpotifyWebAPI();
                $api->setAccessToken($user->spotify_token);

                $api->play(false, [
                    'uris' => [$item->spotify_uri],
                ]);
            } catch (\Exception $e) {
                //
            }

        }

        $item->status = "I";
        $item->save();

        return redirect()->route('home');
    }
}