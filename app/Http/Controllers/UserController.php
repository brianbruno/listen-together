<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 10:06
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function trocarStatus() {
        $user = Auth::user();

        $novoValor = boolval($user->spotify_status) ? 0 : 1;

        $user->spotify_status = $novoValor;
        $user->save();

        return redirect()->route('home');
    }

}