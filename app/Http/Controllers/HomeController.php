<?php

namespace App\Http\Controllers;

use App\Fila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SpotifyWebAPI;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {

        try {
            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(Auth::user()->spotify_token);

            $currentTrack = $api->getMyCurrentTrack();
            $currentTrack = $currentTrack->item->name. " - " . $currentTrack->item->artists[0]->name;

            $fila = Fila::where('name', '=', 'default')->first();

            if ($fila != null) {
                $itensFila = $fila->itens()->limit(15)->get();
            } else {
                $itensFila = [];
            }
            /*
                    $api->play(false, [
                        'uris' => ['spotify:track:7xGfFoTpQ2E7fRF5lN10tr'],
                    ]);*/


            return view('home', ['track' => $currentTrack, 'itensFila' => $itensFila]);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            return redirect('authspotify');
        }

    }
}
