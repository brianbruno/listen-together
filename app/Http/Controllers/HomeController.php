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
            AuthSpotifyController::refreshToken();

            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(Auth::user()->spotify_token);

            $currentTrack = $api->getMyCurrentTrack();

            if ($currentTrack != null) {
                $currentTrack = $currentTrack->item->name. " - " . $currentTrack->item->artists[0]->name;
            } else {
                $currentTrack = "";
            }

            $fila = Fila::where('name', '=', 'default')->first();

            if ($fila != null) {
                $itensFila = $fila->itens()->where('status', 'N')->limit(15)->get();
            } else {
                $itensFila = [];
            }

            return view('home', ['track' => $currentTrack, 'itensFila' => $itensFila]);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            return redirect()->route('authspotify', ['id_user' => Auth::user()->id]);
        }

    }
}
