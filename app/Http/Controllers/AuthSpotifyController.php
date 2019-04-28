<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 09:32
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SpotifyWebAPI;

class AuthSpotifyController extends Controller {

    public function autorizar($id_user) {
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


        Storage::disk('local')->put('id_user', $id_user);

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
        $refreshToken = $session->getRefreshToken();

        $id_user = Storage::get('id_user');
        $user = User::find($id_user);
        $user->spotify_token = $accessToken;
        $user->spotify_refreshtoken = $refreshToken;
        $user->save();

        echo "<script>window.close();</script>";
//        return redirect()->route('home');
    }

    public static function refreshToken($user = null) {
        $url = env('APP_URL');

        $session = new SpotifyWebAPI\Session(
            '936b7eace3ed43059613cd0ac9a18ec2',
            'd284a72f3b3543a392486f3d9ddcfe23',
            $url.'/gravartoken'
        );

        if ($user == null) {
            $user = Auth::user();
        }

        $session->refreshAccessToken($user->spotify_refreshtoken);
        $accessToken = $session->getAccessToken();
        $user->spotify_token = $accessToken;
        $user->save();
    }

}