@extends('voyager::welcome')

@section('content')

    <h2>Sign In Below:</h2>

    <form action="{{ route('voyager.login') }}" method="POST">
        {{ csrf_field() }}
        <div class="group">
            <input type="text" name="email" value="{{ old('email') }}" required>
            <span class="highlight"></span>
            <span class="bar"></span>
            <label><i class="glyphicon glyphicon-user"></i><span class="span-input"> E-mail</span></label>
        </div>

        <div class="group">
            <input type="password" name="password" required>
            <span class="highlight"></span>
            <span class="bar"></span>
            <label><i class="glyphicon glyphicon-lock"></i><span class="span-input"> Password</span></label>
        </div>

        <button type="submit" class="btn btn-block login-button">
            <span class="signingin hidden"><span class="glyphicon glyphicon-refresh"></span> Loggin in...</span>
            <span class="signin">Login</span>
        </button>
    </form>

    @if(!$errors->isEmpty())
        <div class="alert alert-black">
            <ul class="list-unstyled">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a class="btn btn-link" href="{{ route('voyager.password_request_form') }}">Forgot Your Password?</a>

@endsection