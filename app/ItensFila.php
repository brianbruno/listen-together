<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItensFila extends Model {

    protected $table = 'itens_fila';

    public function fila() {
        return $this->belongsTo('App\Fila', 'id_fila');
    }
}
