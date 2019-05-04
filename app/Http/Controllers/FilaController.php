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
use App\Musica;
use App\MusicaLikes;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SpotifyWebAPI;

class FilaController extends Controller {

    public function index($idFila) {
        return view('app.fila', ['idFila' => $idFila]);
    }

    public function buscarMusica(\Illuminate\Http\Request $request) {

        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            if ($request->busca) {
                throw new \Exception("Frase de busca vazia.");
            }

            $musicas = Musica::buscarMusicasSpotify($request->busca);

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
            $fila = Fila::find($request->id_fila);

            if ($fila == null) {
                throw new \Exception("Fila não cadastrada");
            }

            $musica = Musica::encontrarUriMusica($request->uri);

            $itemFila = new ItensFila();
            $itemFila->id_fila = $fila->id;
            $itemFila->id_user = Auth::user()->id;
            $itemFila->id_musica = $musica->id;

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
                  SELECT musicas.name name, itens_fila.id, filas.name queue_name, users.name username FROM itens_fila 
                  LEFT JOIN filas ON itens_fila.id_fila = filas.id 
                  LEFT JOIN users ON itens_fila.id_user = users.id
                  LEFT JOIN musicas ON musicas.id = itens_fila.id_musica
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
            'plays'   => 0,
            'id_fila' => '',
            'nome_fila' => '',
            'liked'    => true,
            'likes'    => 0,
            'id'       => ''
        ];


        try {

            $user = Auth::user();

            if (empty($idFila)) {
                $idFila = Auth::user()->id_fila;
            }

            $resultado = DB::select("
                  SELECT musicas.name name, musicas.spotify_uri, musicas.spotify_image, users.id, filas.name queue_name, users.name username,
                         COUNT(historico_musicas.id) plays, musicas.id id_musica FROM itens_fila
                  LEFT JOIN filas ON itens_fila.id_fila = filas.id 
                  LEFT JOIN users ON itens_fila.id_user = users.id
                  LEFT JOIN musicas ON musicas.id = itens_fila.id_musica
                  LEFT JOIN historico_musicas ON musicas.id = historico_musicas.id_musica
                  WHERE itens_fila.status = 'I'
                  AND filas.id = :fila
                  GROUP BY musicas.name, musicas.spotify_uri, musicas.spotify_image, users.id, filas.name, users.name, musicas.id
                  ORDER BY itens_fila.id
                  LIMIT 1", ['fila' => $idFila]);


            if (sizeof($resultado) > 0) {

                $likesMusica = MusicaLikes::where('id_musica', $resultado[0]->id_musica)->count();
                $likesUserMusica = MusicaLikes::where('id_user', $user->id)->where('id_musica', $resultado[0]->id_musica)->count();

                $retorno['liked'] = boolval($likesUserMusica);
                $retorno['likes'] = $likesMusica;
                $retorno['data'] = $resultado[0]->name;
                $retorno['autor'] = "por " . $resultado[0]->username;
                $retorno['image'] = $resultado[0]->spotify_image;
                $retorno['id_fila'] = $idFila;
                $retorno['nome_fila'] = $resultado[0]->queue_name;
                $retorno['id'] = $resultado[0]->id_musica;
                $retorno['plays'] = $resultado[0]->plays;
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
                  WHERE filas.public = 1
                  ORDER BY votos DESC, rand()
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