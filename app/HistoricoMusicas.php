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

    public function musica() {
        return $this->belongsTo('App\Musica', 'id_musica');
    }

    public function getMusicNameAttribute() {
        $musica = $this->musica()->first();
        return $musica->name;
    }
}
