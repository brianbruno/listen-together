<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 20/04/2019
 * Time: 00:44
 */

namespace App\Http\Controllers;


use App\User;

class AdminController extends Controller {

    public function index() {
        $usuariosOnline = User::where('spotify_status', '=', '1')->count();

        return view('admin.index', ['usuariosOnline' => $usuariosOnline]);
    }

}