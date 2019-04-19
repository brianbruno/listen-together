<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 19/04/2019
 * Time: 12:10
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Artisan;

class HostController extends Controller {

    public function migrate() {

        Artisan::call('migrate');
        echo "Done.";
    }


}