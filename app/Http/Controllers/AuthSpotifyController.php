<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 09:32
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SpotifyWebAPI;

class AuthSpotifyController extends Controller {

    public function autorizar(Request $request, $id_user = null) {
        $url = env('APP_URL', 'http://localhost:8000');

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

        if (empty($id_user)) {
            $id_user = Auth::user()->id;
        }

//        $request->session()->put('id_user', $id_user);
        Storage::disk('local')->put('id_user', $id_user);

        header('Location: ' . $session->getAuthorizeUrl($options));
        die();
    }

    public function gravarCodigo(Request $request) {
        $url = env('APP_URL', 'http://localhost:8000');

        $session = new SpotifyWebAPI\Session(
            '936b7eace3ed43059613cd0ac9a18ec2',
            'd284a72f3b3543a392486f3d9ddcfe23',
            $url.'/gravartoken'
        );


        $session->requestAccessToken($_GET['code']);

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        $id_user = Storage::get('id_user');
//        $id_user = $request->session()->get('key');
        $user = User::find($id_user);
        $user->spotify_token = $accessToken;
        $user->spotify_refreshtoken = $refreshToken;
        $user->save();

//        echo "<script>window.close();</script>";
        return redirect()->route('home');
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