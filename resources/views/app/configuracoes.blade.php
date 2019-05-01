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
            </div>
        </div>
    </div>
@endsection
