<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function filas() {
        return $this->hasMany('App\Fila', 'id_user', 'id');
    }

    public function historicos() {
        return $this->hasMany('App\HistoricoMusicas', 'id_user', 'id');
    }

    private function afinidade($texto) {
        $total = $this->historicos()->whereHas("musica", function($q) use ($texto){
            $q->where('name', 'like', '%' . $texto . '%');
        })->count();

        return $total;
    }

    public function afinidadeArtista($nomeArtista) {
        return $this->afinidade($nomeArtista);

    }

    public function afinidadeMusica($nomeMusica) {
        return $this->afinidade($nomeMusica);
    }

}
