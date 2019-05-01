<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItensFila extends Model {

    protected $table = 'itens_fila';

    public function fila() {
        return $this->belongsTo('App\Fila', 'id_fila');
    }

    public function user() {
        return $this->belongsTo('App\User', 'id_user');
    }

    public function musica() {
        return $this->belongsTo('App\Musica', 'id_musica');
    }

    public static function adicionarMusica($id_fila, $id_user, $id_musica) {
        $itemFila = ItensFila::where('id_fila', $id_fila)
                                ->where('id_musica', $id_musica)->first();

        if (empty($itemFila)) {
            $itemFila = new ItensFila();
            $itemFila->id_fila = $id_fila;
            $itemFila->id_user = $id_user;
            $itemFila->id_musica = $id_musica;
            $itemFila->save();
        }

        return $itemFila;
    }



}
