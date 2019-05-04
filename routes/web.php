<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::get('/authspotify/{id_user?}', 'AuthSpotifyController@autorizar')->name('autorizar-spotify');
Route::get('/authspotify/{id_user?}', 'AuthSpotifyController@autorizar')->name('authspotify');

Route::get('/gravartoken', 'AuthSpotifyController@gravarCodigo');

Route::get('/buscarmusicas', 'FilaController@buscarMusicas')->name('buscar-musicas');

Route::get('/migrate', 'HostController@migrate');

Route::middleware(['auth'])->group(function () {

    Route::prefix('api')->group(function () {
        Route::get('/getproximasmusicas/{idfila}', 'FilaController@proximasMusicas');
        Route::get('/getmusicaatual/{idfila?}', 'FilaController@getMusicaAtual');
        Route::get('/trocarstatus', 'UserController@trocarStatus')->name('trocar-status');
        Route::get('/getuserdata', 'UserController@getUserData');
        Route::get('/filas', 'FilaController@getFilas');
        Route::post('/votar/{idfila}', 'FilaController@votarFila');
        Route::post('/like/{idmusica}', 'MusicaLikeController@likeMusica');
        Route::post('/buscarmusica', 'FilaController@buscarMusica');
        Route::post('/adicionarmusica', 'FilaController@adicionarMusica');
        Route::post('/removermusica', 'FilaController@removerMusica');
        Route::post('/trocarfila', 'UserController@trocarFila');
        Route::post('/proximamusica', 'FilaController@proximaMusica');
    });

    Route::get('/configuracoes', 'ConfiguracaoController@index')->name('configuracoes');
    Route::get('/fila/{idFila}', 'FilaController@index');
    Route::get('/configuracoes/copiarplaylists', 'ConfiguracaoController@copiarPlaylists')->name('configuracoes.copiarplaylists');
    Route::get('/criarfila', 'FilaController@getFilasUser')->name('filas-user');
    Route::post('/salvarfila', 'FilaController@salvarFila')->name('salvar-fila');
    Route::get('/apagarfila/{id}', 'FilaController@apagarFila')->name('apagar-fila');

    Route::namespace('Integration')->group(function () {
        Route::get('twitter/login', 'TwitterController@signIn')->name('twitter.login');
        Route::get('twitter/callback', 'TwitterController@callback')->name('twitter.callback');
        Route::get('twitter/error', 'TwitterController@error')->name('twitter.error');
        Route::get('twitter/logout', 'TwitterController@logout')->name('twitter.logout');
    });
});

Route::middleware(['auth', 'admin'])->group(function () {

    Route::prefix('admin')->group(function () {
        Route::get('/', 'AdminController@index');
    });

});