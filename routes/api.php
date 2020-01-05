<?php

use Illuminate\Http\Request;

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');

        Route::get('/getmusicaatual/{idfila?}', 'FilaController@getMusicaAtual');
        Route::get('/getproximasmusicas/{idfila}', 'FilaController@proximasMusicas');
        Route::post('/buscarmusica', 'FilaController@buscarMusica');
        Route::post('/adicionarmusica', 'FilaController@adicionarMusica');
        Route::get('/filas', 'FilaController@getFilas');
        Route::post('/trocarfila', 'UserController@trocarFila');
        Route::post('/buscarfilas', 'FilaController@buscarFila');
        Route::get('/like/{idmusica}', 'MusicaLikeController@likeMusica');
        Route::post('/removermusica', 'FilaController@removerMusica');
        Route::get('/user/filas', 'FilaController@getUserFilas');
        Route::get('/user/fila/{idFila}', 'FilaController@getInfoFila');
      
    });
});

Route::group([
    'middleware' => 'auth:api'
], function() {
//    Route::get('/getmusicaatual/{idfila}', 'FilaController@getMusicaAtual');
});