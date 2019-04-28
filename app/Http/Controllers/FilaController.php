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
use App\Jobs\FinalizarMusica;
use App\Jobs\ProcessarFilas;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SpotifyWebAPI;

class FilaController extends Controller {

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

            $fila = Fila::find($request->id_fila);

            if ($fila == null) {
                throw new \Exception("Fila não cadastrada");
            }

            $itemFila = new ItensFila();
            $itemFila->id_fila = $fila->id;
            $itemFila->id_user = Auth::user()->id;
            $itemFila->name = $track->artists[0]->name. " - " . $track->name;
            $itemFila->spotify_uri = $track->uri;
            $itemFila->spotify_id = $track->id;
            $itemFila->ms_duration = $track->duration_ms;
            $itemFila->save();

            event(new MusicaAdicionada($itemFila));

            $totalItens = $fila->itens()->count();

            if ($totalItens == 3) {
                ProcessarFilas::dispatch($fila);
            }

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

            $fila = Fila::find($itemFila->id_fila);

            if ($fila->itens()->count()-1 < 3) {
                $retorno['message'] = 'Uma fila precisa ter pelo menos 3 músicas. Adicione para remover!!';
            } else {
                $itemFila->delete();

                event(new MusicaRemovida());

                $retorno['message'] = 'Operação realizada com sucesso.';
                $retorno['status'] = true;
            }

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

    public function proximasMusicas($idFila) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $results = DB::select("
                  SELECT itens_fila.name name, itens_fila.id, filas.name queue_name, users.name username FROM itens_fila 
                  LEFT JOIN filas ON itens_fila.id_fila = filas.id 
                  LEFT JOIN users ON itens_fila.id_user = users.id
                  WHERE itens_fila.status = 'N'
                  AND filas.id = :fila
                  ORDER BY itens_fila.id
                  LIMIT 5", ['fila' => $idFila]);

            $retorno['data'] = $results;
            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);
    }

    public function getMusicaAtual($idFila = null) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
            'autor'   => '',
            'image'   => '',
            'id_fila' => '',
        ];


        try {

            if (empty($idFila)) {
                $idFila = Auth::user()->id_fila;
            }

            $resultado = DB::select("
                  SELECT itens_fila.name name, itens_fila.spotify_uri, users.id, filas.name queue_name, users.name username FROM itens_fila 
                  LEFT JOIN filas ON itens_fila.id_fila = filas.id 
                  LEFT JOIN users ON itens_fila.id_user = users.id
                  WHERE itens_fila.status = 'I'
                  AND filas.id = :fila
                  ORDER BY itens_fila.id
                  LIMIT 1", ['fila' => $idFila]);


            if (sizeof($resultado) > 0) {
                $api = new SpotifyWebAPI\SpotifyWebAPI();
                $api->setAccessToken(Auth::user()->spotify_token);

                $track = $api->getTrack($resultado[0]->spotify_uri);

                $retorno['data'] = $resultado[0]->name;
                $retorno['autor'] = "por " . $resultado[0]->username;
                $retorno['image'] = $track->album->images[1]->url;
                $retorno['id_fila'] = $idFila;
                $retorno['message'] = 'Dados recuperados com sucesso.';
                $retorno['status'] = true;
            } else {
                $retorno['message'] = 'Não foi possível recuperar a música atual.';
            }
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);

    }

    public function getFilas() {
        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];

        try {

            $results = DB::select(DB::raw("
                  SELECT filas.id, filas.name, filas.avaliacao, filas.capa_fila, filas.descricao, users.name username
                  FROM filas
                  LEFT JOIN users ON users.id = filas.id_user
                  WHERE filas.status = 'A'
                  ORDER BY votos DESC
                  LIMIT 50"));

            $retorno['data'] = $results;
            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);
    }

    public function getFilasUser() {

        $user = Auth::user();

        $filas = $user->filas()->get();

        return view('app.criarfila', ['filas' => $filas]);

    }

    public function salvarFila(\Illuminate\Http\Request $request) {
        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];

        try {
            $user = Auth::user();
            $fila = new Fila();

            $fila->id_user = $user->id;
            $fila->name = $request->name;
            $fila->descricao = $request->descricao;
            $fila->status = 'A';
            $fila->capa_fila = 'https://i.scdn.co/image/e450eb7b7b9616a40d01e23d9ef6d6f52714f912';
            $fila->save();

            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
            var_dump($e->getMessage());die;
        }

        return redirect()->route('filas-user');
    }

    public function apagarFila($id){
        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];

        try {

            ItensFila::where('id_fila', $id)->delete();
            $fila = Fila::find($id);
            $fila->delete();

            $retorno['message'] = 'Dados recuperados com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
            var_dump($e->getMessage());die;
        }

        return redirect()->route('filas-user');
    }

    public function votarFila($idfila) {
        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $fila = Fila::find($idfila);

            $fila->votos = $fila->votos+1;
            $fila->save();

            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);
    }

    public function proximaMusica(\Illuminate\Http\Request $request) {
        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $fila = Fila::find($request->idFila);

            $emExecucao = $fila->itens()->where('status', '=', 'I')->first();

            if ($emExecucao != null) {
                dispatch((new FinalizarMusica($emExecucao))->onQueue('system'));
            }

            $retorno['message'] = 'Operação realizada com sucesso.';
            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);
    }

}