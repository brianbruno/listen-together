<?php

namespace App\Http\Controllers;

use App\Jobs\CopiarPlaylists;
use Illuminate\Support\Facades\Auth;


class ConfiguracaoController extends Controller {

    public function index() {
        return view('app.configuracoes');
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

}