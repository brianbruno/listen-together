@extends('layouts.auth')

@section('content')
    <div class="">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label>
                <input type="text" name="email" required autocomplete="off"/>
                <div class="label-text">E-mail</div>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </label>
            <label>
                <input type="password" name="password" required />
                <div class="label-text">Password</div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </label>
            <button type="submit" class="login">Login</button>
        </form>
        <div class="text-center">
            <a href="{{ route('register') }}"><button class="register">Register</button></a>
        </div>
    </div>
@endsection