<?php

namespace App\Console\Commands;

use App\ItensFila;
use App\User;
use Illuminate\Console\Command;
use SpotifyWebAPI;

class AutoPlay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:autoplay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia as músicas automaticamente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        while (true) {
            $item = ItensFila::where('status', '=', 'N')->orderBy('id')->first();

            if ($item == null) {
                break;
            }

            $users = User::where('spotify_token', '<>', null)->where('spotify_status', '1')->get();

            foreach ($users as $user) {
                try {
                    $api = new SpotifyWebAPI\SpotifyWebAPI();
                    $api->setAccessToken($user->spotify_token);

                    $api->play(false, [
                        'uris' => [$item->spotify_uri],
                    ]);
                } catch (\Exception $e) {
                    $this->line('');
                    $this->error('User: ' . $user->id . ' - ' . $user->name);
                    $this->error($e->getMessage());
                    $this->line('');
                }
            }
            $item->status = "I";
            $item->save();
            $this->line('');
            $this->info('Song started: '.$item->name);

            $tempoTotalSegundos = $item->ms_duration / 1000;
            $bar = $this->output->createProgressBar($tempoTotalSegundos);

            $tempo = 1;

            $bar->start();

            while ($tempo < $tempoTotalSegundos) {
                sleep(1);
                $tempo++;
                $bar->advance();
            }

            $bar->finish();

            $item->status = "F";
            $item->save();
        }

    }
}
