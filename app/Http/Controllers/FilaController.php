<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 10:51
 */

namespace App\Http\Controllers;

use App\Events\MusicaAdicionada;
use App\Events\MusicaRemovida;
use App\Fila;
use App\ItensFila;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SpotifyWebAPI;

class FilaController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function buscarMusica(\Illuminate\Http\Request $request) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {


            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(Auth::user()->spotify_token);

            $results = $api->search($request->busca, 'track');

            $musicas = $results->tracks->items;

            $retorno['data'] = $musicas;
            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }

    public function adicionarMusica(\Illuminate\Http\Request $request) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(Auth::user()->spotify_token);
            $track = $api->getTrack($request->uri);

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

            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }

    public function removerMusica(\Illuminate\Http\Request $request) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $itemFila = ItensFila::find($request->id);

            $itemFila->delete();

            event(new MusicaRemovida());

            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

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
    
    public function proximasMusicas() {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $results = DB::select(DB::raw("
                  SELECT itens_fila.name name, itens_fila.id, filas.name queue_name, users.name username FROM itens_fila 
                  LEFT JOIN filas ON itens_fila.id_fila = filas.id 
                  LEFT JOIN users ON itens_fila.id_user = users.id
                  WHERE itens_fila.status = 'N'
                  ORDER BY itens_fila.id
                  LIMIT 50"));

            $retorno['data'] = $results;
            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);
    }

    public function getMusicaAtual() {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $resultado = DB::select(DB::raw("
                  SELECT itens_fila.name name, users.id, filas.name queue_name, users.name username FROM itens_fila 
                  LEFT JOIN filas ON itens_fila.id_fila = filas.id 
                  LEFT JOIN users ON itens_fila.id_user = users.id
                  WHERE itens_fila.status = 'I'
                  ORDER BY itens_fila.id
                  LIMIT 1"));

            if (sizeof($resultado) > 0) {
                $retorno['data'] = $resultado[0]->name . " por " . $resultado[0]->username;
            } else {
                $retorno['data'] = 'Não foi possível recuperar a música atual.';
            }
            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }
}