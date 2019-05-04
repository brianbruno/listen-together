<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 10:06
 */

namespace App\Http\Controllers;

use App\Events\MusicaIniciada;
use App\Fila;
use App\ItensFila;
use App\Jobs\ProcessarFilas;
use App\Jobs\ProximaMusica;
use App\Jobs\TrocarCapaFila;
use App\Musica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SpotifyWebAPI;
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

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $user = Auth::user();

            $novoValor = boolval($user->spotify_status) ? 0 : 1;

            $user->spotify_status = $novoValor;
            $user->save();

            if (boolval($user->spotify_status)) {
                $api = new SpotifyWebAPI\SpotifyWebAPI();
                $api->setAccessToken($user->spotify_token);
                $devices = $api->getMyDevices()->devices;

                if ($devices != null && sizeof($devices) > 0) {
                    $resultado = DB::select(DB::raw("
                      SELECT itens_fila.spotify_uri FROM itens_fila 
                      WHERE itens_fila.status = 'I'
                      ORDER BY itens_fila.id
                      LIMIT 1"));

                    $api->play($devices[0]->id, [
                        'uris' => [$resultado[0]->spotify_uri],
                    ]);
                } else {
                    throw new \Exception("Nenhum dispositivo conectado.");
                }
            }

            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }

    public function trocarFila(Request $request) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {
            $id_fila = $request->fila;
            $fila = Fila::find($id_fila);

            $musicas = $fila->itens()->get();

            if (sizeof($musicas) > 2) {
                $user = Auth::user();
                $user->id_fila = $id_fila;
                $user->save();

                $item = ItensFila::where('id_fila', $fila->id)->where('status', 'I')->first();

                if ($fila->status == 'I') {
                    $fila->status = 'A';
                    $fila->save();
                    ItensFila::where('status', 'I')->update(['status' => 'F']);
                    ob_start();
                    $retorno['queue_message'] = ProcessarFilas::dispatchNow($fila);
                    ob_end_clean();
                } else {
                    if ($item == null) {
                        $item = $fila->getProximaMusica();
                    }

                    if ($item != null) {
                        $musica = Musica::find($item->id_musica);
                        ProximaMusica::dispatchNow($user, $musica->spotify_uri);
                        $user->spotify_status = '1';
                        $user->save();
                        $item->status = "I";
                        $item->save();
                        event(new MusicaIniciada($item));
                    } else {
                        throw new \Exception("Nenhuma música em reprodução.");
                    }
                }



            } else {
                throw new \Exception("Grupo não possui músicas suficientes para subscrever.");
            }

            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }

    public function getuserdata() {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $user = Auth::user();

            $retorno['data'] = $user;
            $retorno['data']['status'] = boolval($user->spotify_status);
            $retorno['data']['spotify_token'] = $user->spotify_token;
            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }

}