@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <reprodutor :idFila="{{ $idFila }}"></reprodutor>
            </div>
        </div>
    </div>
@endsection
