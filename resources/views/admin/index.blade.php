@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-white">
                <h3>Usuários online: {{ $usuariosOnline }}</h3>
            </div>
        </div>
    </div>
@endsection
