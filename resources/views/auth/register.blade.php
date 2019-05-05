@extends('layouts.auth')

@section('content')
    <div class="">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label>
                <input type="text" name="name" required autocomplete="off"/>
                <div class="label-text">Name</div>
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
                @endif
            </label>
            <label>
                <input type="email" name="email" required autocomplete="off"/>
                <div class="label-text">E-mail</div>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </label>
            <label>
                <input type="password" name="password" required autocomplete="off" />
                <div class="label-text">Password</div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </label>
            <label>
                <input type="password" name="password_confirmation" required />
                <div class="label-text">Password Confirm</div>
            </label>
            <button type="submit" class="login">Register</button>
        </form>
        <div class="text-center">
            <a href="{{ route('login') }}"><button class="register">Login</button></a>
        </div>
    </div>
@endsection