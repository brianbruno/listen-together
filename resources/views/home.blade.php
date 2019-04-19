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
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table>
                            <thead>
                            <tr>
                                <th> música</th>
                                <th> fila</th>
                                <th> iniciar</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($itensFila as $item)
                                <tr>
                                    <td> {{$item->name}} </td>
                                    <td> {{$item->fila()->first()->name }} </td>
                                    <td><a href="{{ route('executar-item', ['id' => $item->id]) }}"><i class="material-icons">play_arrow</i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
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

                        @if(!empty($retornoBusca) && $retornoBusca)
                            <table>
                                <thead>
                                <tr>
                                    <th> música</th>
                                    <th> artista</th>
                                    <th> adicionar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($musicas as $musica)
                                    <tr>
                                        <td> {{ $musica->name }} </td>
                                        <td> {{ $musica->artists[0]->name }}</td>
                                        <td> <a href="{{ route('adicionar-musica-fila', ['trackid' => $musica->id, 'fila' => 'default'] ) }}" role="button" class="btn btn-primary btn-sm">Adicionar</a></td>
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
@endsection
