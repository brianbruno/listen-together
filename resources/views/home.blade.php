@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if($user->utliza_player_site)
                <spotify-player></spotify-player>
                @endif
                {{--<reprodutor></reprodutor>--}}
                <filas></filas>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection
