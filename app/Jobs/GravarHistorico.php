<?php

namespace App\Jobs;

use App\HistoricoMusicas;
use App\Musica;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GravarHistorico implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user, $uri;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $uri) {
        $this->user = $user;
        $this->uri = $uri;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $user = $this->user;
        $uri = $this->uri;

        $historico = new HistoricoMusicas();
        $historico->id_user = $user->id;
        $historico->uri = $uri;
        $historico->data = date('Y-m-d H:i:s');

        $musica = Musica::where('spotify_uri', $uri)->first();

        if (!empty($musica)) {
            $historico->id_musica = $musica->id;
        }

        $historico->save();
    }
}
