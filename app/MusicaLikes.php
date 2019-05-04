<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MusicaLikes extends Model {

    protected $table = 'musica_likes';

    public function musica() {
        return $this->belongsTo('App\Musica', 'id_musica');
    }

    public function user() {
        return $this->belongsTo('App\User', 'id_user');
    }

}
