<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fila extends Model {

    public function itens() {
        return $this->hasMany('App\ItensFila', 'id_fila', 'id');
    }

}
