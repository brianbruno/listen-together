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

Route::get('/authspotify', 'AuthSpotifyController@autorizar')->name('autorizar-spotify');

Route::get('/gravartoken', 'AuthSpotifyController@gravarCodigo');

Route::get('/trocarstatus', 'UserController@trocarStatus')->name('trocar-status');

Route::get('/buscarmusicas', 'FilaController@buscarMusicas')->name('buscar-musicas');

Route::get('/adicionarmusica/trackid/{trackid}/fila/{fila}', 'FilaController@adicionarMusica')->name('adicionar-musica-fila');

Route::get('/executaritem/{id}', 'FilaController@executarMusica')->name('executar-item');
