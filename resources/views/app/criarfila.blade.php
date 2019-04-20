@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-white">
                <h1>Criar Fila</h1>

                <form id="logout-form" action="{{ route('salvar-fila') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-12 col-md-6">
                            <label for="descricao">Nome</label>
                            <input required type="text" name="name" class="form-control input" autocomplete="off"
                                   id="name" aria-describedby="name-help" placeholder="Nome da Fila" maxlength="60" minlength="5">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="descricao">Descrição</label>
                            <input required type="text" name="descricao" class="form-control input" autocomplete="off"
                                   id="descricao" aria-describedby="descricao-help" placeholder="Descrição" maxlength="60" minlength="5">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-outline-success ">
                            Salvar
                        </button>
                    </div>
                </form>

                <div class="text-white">
                    <h1>Minhas Filas</h1>

                    <table class="text-center table text-white">
                        <thead>
                        <tr>
                            <th> nome</th>
                            <th> descricao</th>
                            <th> ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($filas as $fila)
                            <tr>
                                <td> {{ $fila->name }}</td>
                                <td> {{ $fila->descricao }} </td>
                                <td> <a href="{{ route('apagar-fila', ['id' => $fila->id]) }}"><i class="material-icons">close</i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
