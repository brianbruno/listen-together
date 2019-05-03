<?php

namespace App\Jobs;

use App\HistoricoMusicas;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SpotifyWebAPI;
use App\Http\Controllers\AuthSpotifyController;

class ProximaMusica implements ShouldQueue {

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

        try {
            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken($user->spotify_token);
            $devices = $api->getMyDevices()->devices;

            if ($devices != null && sizeof($devices) > 0) {
                $api->play($devices[0]->id, [
                    'uris' => [$this->uri],
                ]);

                dispatch((new GravarHistorico($user, $this->uri))->onQueue('system'));

            } else {
//                $user->spotify_status = 0;
//                $user->save();

                throw new \Exception("Nenhum dispositivo conectado. ".$user->name."\n");
            }

            dispatch((new AtualizarTokenSpotify($user))->onQueue('system'));
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
        }

    }
}
