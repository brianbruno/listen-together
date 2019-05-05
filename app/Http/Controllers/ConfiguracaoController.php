<?php

namespace App\Http\Controllers;

use App\Jobs\CopiarPlaylists;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller {

    public function index() {
        $user = Auth::user();
        return view('app.configuracoes', ['user'=> $user]);
    }

    public function copiarPlaylists() {
        $retorno = [
            'status'  => false,
        ];

        try {
            $user = Auth::user();
            CopiarPlaylists::dispatch($user)->onQueue('system');
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return redirect()->route('configuracoes');
    }

    public function alterarParametros(Request $request) {

        $user = Auth::user();

        $user->spotify_status = $request->spotify_status;
        $user->utliza_player_site = $request->utliza_player_site;

        $user->save();

        return redirect()->to('configuracoes');

    }

}