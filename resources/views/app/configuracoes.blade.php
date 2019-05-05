@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-white">
                <h1>Configurações</h1>

                <div class="row">
                    <div class="col-md-3 col-sm-12">
                        <a href="{{ route('configuracoes.copiarplaylists') }}" class="btn btn-outline-success">Copiar playlists do Spotify</a>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <a href="{{ route('twitter.login') }}" class="btn btn-outline-primary">Conectar com o Twitter</a>
                    </div>
                </div>
                <hr>
                <form method="post" action="{{ route('alterar-parametros') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="box">
                                <p>Ativar músicas automáticas?</p>
                                <label>
                                    <input type="radio" name="spotify_status" {{ boolval($user->spotify_status) ? 'checked' : '' }} value="1">
                                    <span class="yes">Sim</span>
                                </label>
                                <label>
                                    <input type="radio" name="spotify_status" {{ !boolval($user->spotify_status) ? 'checked' : '' }} value="0">
                                    <span class="no">Não</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="box">
                                <p>Ativar reprodutor do site?</p>
                                <label>
                                    <input type="radio" name="utliza_player_site" {{ boolval($user->utliza_player_site) ? 'checked' : '' }} value="1">
                                    <span class="yes">Sim</span>
                                </label>
                                <label>
                                    <input type="radio" name="utliza_player_site" {{ !boolval($user->utliza_player_site) ? 'checked' : '' }} value="0">
                                    <span class="no">Não</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-sm-12">
                            <div class="box">
                                <label>
                                    <input type="submit" checked>
                                    <span class="yes">Salvar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
