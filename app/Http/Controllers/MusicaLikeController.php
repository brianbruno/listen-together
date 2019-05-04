<?php

namespace App\Http\Controllers;

use App\MusicaLikes;
use App\User;
use Illuminate\Support\Facades\Auth;

class MusicaLikeController {

    public function likeMusica($idMusica) {
        $retorno = [
            'message' => 'Não inicializado',
            'status'  => false,
            'data'    => [],
        ];


        try {

            $user = Auth::user();

            $likesMusica = MusicaLikes::where('id_user', $user->id)->where('id_musica', $idMusica)->get();

            if (sizeof($likesMusica) == 0) {
                $musicaLike = new MusicaLikes();
                $musicaLike->id_musica = $idMusica;
                $musicaLike->id_user = $user->id;
                $musicaLike->save();
                $retorno['message'] = 'Você deu like nessa música!';
            } else {
                $like = $likesMusica[0];
                $like->delete();
                $retorno['message'] = 'Você retirou o like da música! :(';
            }

            $retorno['status'] = true;
        } catch(\Exception $e) {
            $retorno['message'] = $e->getMessage();
        }

        return response()->json($retorno, 200);
    }

}