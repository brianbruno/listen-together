<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class ArmazenarTweetsUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:armazenartweets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const HASH_TWEET = '#ouvirjuntos';

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
        $users = User::where('twitter_status', true)->get();

        foreach ($users as $user) {
            $accessToken = $user->twitter_oauth_token;
            $accessSecret = $user->twitter_oauth_token_secret;
            $userFileName = 'tweets'.$user->id.'.txt';

            $request_token = [
                'token'  => $accessToken,
                'secret' => $accessSecret,
            ];

            Twitter::reconfig($request_token);

            $userTimeLine = Twitter::getUserTimeline(['screen_name' => $user->twitter_screen_name, 'count' => 200]);
            $tweets = [];

            foreach ($userTimeLine as $item) {
                if (!$item->retweeted) {
                    $texto = $item->text;
//                    $contemHashTag = strpos($texto, self::HASH_TWEET);
                    $contemHashTag = true;

                    if (strlen($texto) > 5 and $contemHashTag !== false) {
                        $tweets[] = $texto;
                        $tweets[] = "([EOT])";
                    }
                }
            }

            Storage::disk('local')->put($userFileName, implode(PHP_EOL, $tweets));
        }

        return;
    }
}
