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
}
