<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistoricoMusicas extends Model {

    protected $table = 'historico_musicas';

    public function itemFila() {
        return $this->belongsTo('App\ItensFila', 'id_item_fila');
    }

    public function user() {
        return $this->belongsTo('App\User', 'id_user');
    }

}
