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

            return view('home', ['user' => Auth::user()]);

        } catch (\Exception $e) {
            return redirect()->route('authspotify', ['id_user' => Auth::user()->id]);
        }

    }
}
