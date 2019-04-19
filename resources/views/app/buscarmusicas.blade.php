@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        <div class="row">
                            <div class="col-md-8">
                                Dashboard - {{ $track }}
                            </div>
                            <div class="col-md-4">
                                <div class="text-right">
                                    @if(boolval(Auth::user()->spotify_status))
                                        <a role="button" class="btn btn-danger btn-sm" href="{{ route('trocar-status') }}">Desativar</a>
                                    @else
                                        <a role="button" class="btn btn-success btn-sm" href="{{ route('trocar-status') }}">Ativar</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div>
                            <h3>Buscar música</h3>

                            <form action="{{ route('buscar-musicas') }}" method="get">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" name="musica" class="form-control" placeholder="Digite a busca" aria-label="Busca">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Buscar</button>
                                    </div>
                                </div>
                            </form>

                            <div class="container-fluid">
                                @if(!empty($retornoBusca) && $retornoBusca)
                                    <table class="text-center table">
                                        <thead>
                                        <tr>
                                            <th> artista</th>
                                            <th> música</th>
                                            <th> adicionar</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($musicas as $musica)
                                            <tr>
                                                <td> {{ $musica->artists[0]->name }}</td>
                                                <td> {{ $musica->name }} </td>
                                                <td> <a href="{{ route('adicionar-musica-fila', ['trackid' => $musica->id, 'fila' => 'default'] ) }}">
                                                        <i class="material-icons">add</i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
