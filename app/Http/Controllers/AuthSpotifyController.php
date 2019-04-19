<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 09:32
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SpotifyWebAPI;

class AuthSpotifyController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function autorizar() {
        $url = env('APP_URL');

        $session = new SpotifyWebAPI\Session(
            '936b7eace3ed43059613cd0ac9a18ec2',
            'd284a72f3b3543a392486f3d9ddcfe23',
            $url.'/gravartoken'
        );
        $options = [
            'scope' => [
                'user-read-playback-state',
                'streaming',
                'user-read-birthdate',
                'user-read-email',
                'user-read-private'
            ],
        ];

        header('Location: ' . $session->getAuthorizeUrl($options));
        die();
    }

    public function gravarCodigo() {
        $url = env('APP_URL');

        $session = new SpotifyWebAPI\Session(
            '936b7eace3ed43059613cd0ac9a18ec2',
            'd284a72f3b3543a392486f3d9ddcfe23',
            $url.'/gravartoken'
        );


        $session->requestAccessToken($_GET['code']);

        $accessToken = $session->getAccessToken();

        $user = Auth::user();
        $user->spotify_token = $accessToken;
        $user->save();

        return redirect()->route('home');
    }

}