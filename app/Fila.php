<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fila extends Model {

    public function itens() {
        return $this->hasMany('App\ItensFila', 'id_fila', 'id');
    }

    public function getProximaMusica() {
        $musicas = $this->itens()->where('status', '=', 'N')->orderBy('id')->get();

        if (sizeof($musicas) < 2) {
            if (sizeof($musicas) == 0) {
                $item = $this->itens()->orderBy(DB::raw('RAND()'))->first();
            } else {
                $item = $musicas[0];
            }

            $proxItem = $this->itens()->orderBy(DB::raw('RAND()'))->first();
            if ($proxItem != null) {
                $proxItem->status = 'N';
                $proxItem->save();
            }
        } else {
            $item = $musicas[0];
        }

        return $item;
    }

}
