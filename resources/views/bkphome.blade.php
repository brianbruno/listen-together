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
                        </div>
                        <div>
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <table class="table">
                                <thead>
                                <tr>
                                    <th> música</th>
                                    <th> fila</th>
                                    <th> usuário</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($itensFila as $item)
                                    <tr>
                                        <td> {{$item->name}} </td>
                                        <td> {{$item->fila()->first()->name }} </td>
                                        <td> {{$item->user()->first()->name }} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
